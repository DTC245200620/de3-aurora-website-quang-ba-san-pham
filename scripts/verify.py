"""Integration checks executed inside the private Compose networks."""
import json,time,urllib.request,urllib.parse,urllib.error,sys
def request(url,data=None,host=None):
    headers={"Host":host} if host else {}
    if data is not None: headers.update({"Content-Type":"application/x-www-form-urlencoded","Cookie":"wordpress_test_cookie=WP%20Cookie%20check"})
    try:
        with urllib.request.urlopen(urllib.request.Request(url,data=data,headers=headers),timeout=12) as r:return r.status,r.read().decode(),dict(r.headers)
    except urllib.error.HTTPError as e:return e.code,e.read().decode(),dict(e.headers)
def get(url):return json.loads(request(url)[1])
def prom(q):return get("http://prometheus:9090/api/v1/query?"+urllib.parse.urlencode({"query":q}))["data"]["result"]
def check(condition,text):
    if not condition:raise AssertionError(text)
    print("PASS "+text,flush=True)
def main():
    status,html,headers=request("http://nginx:8080/",host="localhost:8080")
    check(status==200 and html.count('class="product-card"')>=1,"Website serves product catalog")
    for key in ["X-Content-Type-Options","X-Frame-Options","Referrer-Policy","Permissions-Policy","Content-Security-Policy"]:
        check(any(k.lower()==key.lower() for k in headers),"Security header "+key)
    for path in ["/san-pham/","/gioi-thieu/","/lien-he/"]:
        check(request("http://nginx:8080"+path,host="localhost:8080")[0]==200,"Public page "+path)
    check(request("http://phpmyadmin/")[0]==200,"phpMyAdmin reachable")
    check(request("http://grafana:3000/api/health")[0]==200,"Grafana health")
    targets=get("http://prometheus:9090/api/v1/targets")["data"]["activeTargets"]
    check(len(targets)==4 and all(t["health"]=="up" for t in targets),"All four Prometheus targets UP")
    for metric in ["nginx_up","mysql_up"]:
        values=prom(metric);check(bool(values) and all(float(v["value"][1])==1 for v in values),metric+" live service")
    label='container_label_com_docker_compose_project="aurora-de3",container_label_com_docker_compose_service!=""'
    for query in ["container_memory_working_set_bytes{"+label+"}","rate(container_cpu_usage_seconds_total{"+label+"}[2m])"]:
        values=prom(query);services={v["metric"].get("container_label_com_docker_compose_service") for v in values}
        check({"wordpress","mysql","nginx"}.issubset(services),"Real container metrics "+query.split("{")[0])
    check(request("http://nginx:8080/aurora-demo-not-found/",host="localhost:8080")[0]==404,"Real HTTP 404")
    request("http://nginx:8080/wp-login.php",urllib.parse.urlencode({"log":"aurora_monitor_demo","pwd":"invalid-demo-password","testcookie":"1"}).encode(),"localhost:8080")
    queries=['{project="aurora-de3",service="nginx",stream="access"} | json','{project="aurora-de3",service="nginx",stream="access"} | json | status >= 400','{project="aurora-de3",service="wordpress",stream="events"} | json | event="login_failed"']
    for q in queries:
        found=False
        for _ in range(20):
            result=get("http://loki:3100/loki/api/v1/query_range?"+urllib.parse.urlencode({"query":q,"limit":5,"start":str(int((time.time()-3600)*1e9))}))
            if result.get("data",{}).get("result"):found=True;break
            time.sleep(2)
        check(found,"LogQL "+q)
    print(json.dumps({"result":"PASS","time":time.strftime("%Y-%m-%dT%H:%M:%SZ",time.gmtime())}))
if __name__=="__main__":
    try:main()
    except Exception as e:print("FAIL "+str(e),file=sys.stderr);sys.exit(1)
