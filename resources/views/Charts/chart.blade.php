<html>
<title></title>
<body>
<div id="cost-div"></div>
<div id="aroon-div"></div>
<div id="coindesk-widget" data-size="mpu" data-align="right" ></div>

</body>
<script type="text/javascript" src="//widget.coindesk.com/bpiticker/coindesk-widget.min.js"></script>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script type="text/javascript">
    console.log("hello");
    var cost = [];
    var time = [];
    var endpointURL = "https://koinex.in/api/ticker";
    setInterval(function(){
        var req = new XMLHttpRequest();
        req.addEventListener('load', complete, false);
        req.open('GET', endpointURL , true);
        req.send();
    },10000);
    function complete(e){
        var BTC2USD = JSON.parse(this.response)['prices'];
        var xrp = BTC2USD['XRP'];
        time.push(new Date());
        cost.push(xrp);
        console.log("XRP = " + xrp);
        var data = [
            {
                x: time,
                y: cost,
                type: 'scatter'
            }
        ];
//        var y = calculateAroonUp(cost,time);
        Plotly.newPlot('cost-div', data);
    }
</script>
</html>