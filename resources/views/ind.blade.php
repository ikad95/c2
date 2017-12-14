<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>


<div id="container" style="min-height: 200%; min-width: 310px"></div>
</body>
<script src="https://www.gstatic.com/firebasejs/4.8.0/firebase.js"></script>

<script type="text/javascript">

$(document).ready( function () {

  

  var PUSH_CHARS = "-0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz";
  function decode(id) {
    id = id.substring(0,8);
    var timestamp = 0;
    for (var i=0; i < id.length; i++) {
      var c = id.charAt(i);
      timestamp = timestamp * 64 + PUSH_CHARS.indexOf(c);
    }
    var dateIST = new Date(timestamp);
    dateIST.setHours(dateIST.getHours() + 5); 
    dateIST.setMinutes(dateIST.getMinutes() + 30);
    return dateIST.getTime();
    return date.getHours()+""+date.getMinutes()+""+date.getSeconds();
  }

  var config = {
    apiKey: "AIzaSyD_cq2D3MOWYVn9ABotalIu8j6cRxXrx40",
    authDomain: "aroon-40594.firebaseapp.com",
    databaseURL: "https://aroon-40594.firebaseio.com",
    projectId: "aroon-40594",
    storageBucket: "aroon-40594.appspot.com",
    messagingSenderId: "45201910878"
  };
  firebase.initializeApp(config);

  var database = firebase.database();

	var body = document.body,
    html = document.documentElement;
	var height = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );

  $('#container').css({'height' :height-18});
  window.addEventListener('resize', function(){
    var height = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );

    $('#container').css({'height' :height-18});
  });

  data = [];
  movingAverageSlow = [];
  movingAverageFast = [];
  volume = [];
  ohlc = [];
  macd = [];
  highest_bid = [];
  lowest_ask = [];
  spread = [];
  console.log(data);

  var getData = function (callback){
    var t,o,h,l,c;
    database.ref('data/').once("value")
                    .then(function(snapshot) {
                       json = snapshot.val();
                       (Object.keys(json)).forEach(function(k){
                        // console.log(typeof(json[k]));
                        var obj = JSON.parse(json[k]);
                        t = decode(k);
                        p = parseFloat(obj['prices']['XRP']);
                        o = parseFloat(obj['stats']['XRP']['min_24hrs']);
                        h = parseFloat(obj['stats']['XRP']['highest_bid']);
                        l = parseFloat(obj['stats']['XRP']['lowest_ask']);
                        c = parseFloat(obj['stats']['XRP']['max_24hrs']);
                        v = parseFloat(obj['stats']['XRP']['vol_24hrs']);
                        data.push([t,p]);
                        volume.push([t,v]);
                        highest_bid.push([t,h]);
                        lowest_ask.push([t,l]);
                        ohlc.push([t,p,h,l,p]);
                        spread.push([t, h - l])
                      });
                      var tot = 0;
                      //Simple moving average data is based on last 30 mins // increase when data gathered is enough
                      var N = 100;
                      for(var i = 0; i < N; i++){       
                        movingAverageFast.push([data[i][0],tot/i]);
                        tot+=data[i][1];
                      }
                      console.log(data.length);
                      for(var i = N; i < data.length; i++){
                        movingAverageFast.push([data[i][0],(tot/N)]);
                        tot+=data[i][1];
                        tot-=data[i-N][1];
                      }

                      var N = 200;
                      tot = 0;
                      for(var i = 0; i < N; i++){       
                        movingAverageSlow.push([data[i][0],tot/i]);
                        tot+=data[i][1];
                      }
                      console.log(data.length);
                      for(var i = N; i < data.length; i++){
                        movingAverageSlow.push([data[i][0],(tot/N)]);
                        tot+=data[i][1];
                        tot-=data[i-N][1];
                      }
                      for( var i = 0; i < movingAverageSlow.length; i++){
                        macd.push([movingAverageSlow[i][0], movingAverageFast[i][1]-movingAverageSlow[i][1]]);
                      }
                      callback();
                     });
    //return [t,o,h,l,c];
  }


	  // create the chart
    var f1 = function(){
    	Highcharts.stockChart('container', {

		chart: {
	        events: {
	            load: function () {
	                // set up the updating of the chart each second
	                // var data = this.data;
	                var series = this.series[0];
	                var updown = this.series[1];
	                var aroon = this.series[2];                  
                      
	                // 	console.log("hi");
	                // 	var temp = Math.round(Math.random() * 1000);
	                //     var t = (new Date()).getTime(), // current time
	                //         o = temp + 100,
	                //     	h = temp + 200,
	                //     	l = temp,
	                //         c = temp + 50;
	                //     series.addPoint([t,o,h,l,c]);
	                //     updown.addPoint([t,h]);
	                //     aroon.addPoint([t,l]);
	                // }, 100);
	            }
	        }
	    },
        rangeSelector: {
            selected: 2
        },

        title: {
            text: 'XRP'
        },
		yAxis: [{
            labels: {
                align: 'right',
                x: -3
            },
            title: {
                text: 'XRP/INR'
            },
            height: '30%',
            lineWidth: 8,
            resize: {
                enabled: true
            }
        },
        {
            labels: {
                align: 'right',
                x: -3
            },
            title: {
                text: 'MACD'
            },
            top: '35%',
            height: '40%',
            offset: 0,
            lineWidth: 8,
            resize: {
                enabled: true
            }
        },
        // {
        //     labels: {
        //         align: 'right',
        //         x: -3
        //     },
        //     title: {
        //         text: 'Bid-Ask Spread (Liquidity)'
        //     },
        //     top: '60%',
        //     height: '20%',
        //     offset: 0,
        //     lineWidth: 8,
        //     resize: {
        //         enabled: true
        //     }
        // }
        ],
        series: [
        {
            name: 'Rate',
            data: data,
            // dataGrouping: {
            //     units: [[
            //         'week', // unit name
            //         [1] // allowed multiples
            //     ], [
            //         'month',
            //         [1, 2, 3, 4, 6]
            //     ]]
            // }
        },
        // {
        //     name: 'Moving Average Slow',
        //     data: movingAverageSlow,
        //     yAxis: 1,
        //     // dataGrouping: {
        //     //     units: [[
        //     //         'week', // unit name
        //     //         [1] // allowed multiples
        //     //     ], [
        //     //         'month',
        //     //         [1, 2, 3, 4, 6]
        //     //     ]]
        //     // }
        // },
        {
            type: 'column',
            name: 'MACD',
            data: macd,
            yAxis: 1,
            // dataGrouping: {
            //     units: [[
            //         'week', // unit name
            //         [1] // allowed multiples
            //     ], [
            //         'month',
            //         [1, 2, 3, 4, 6]
            //     ]]
            // }
        },
        // {
        //     name: 'Moving Average Fast',
        //     data: movingAverageFast,
        //     yAxis: 1,
        //     // dataGrouping: {
        //     //     units: [[
        //     //         'week', // unit name
        //     //         [1] // allowed multiples
        //     //     ], [
        //     //         'month',
        //     //         [1, 2, 3, 4, 6]
        //     //     ]]
        //     // }
        // },
        // {
        //     name: 'ask',
        //     data: lowest_ask,
        //     yAxis: 2,
        //     // dataGrouping: {
        //     //     units: [[
        //     //         'week', // unit name
        //     //         [1] // allowed multiples
        //     //     ], [
        //     //         'month',
        //     //         [1, 2, 3, 4, 6]
        //     //     ]]
        //     // }
        // },
        // {
        //     name: 'bid',
        //     data: highest_bid,
        //     yAxis: 2,
        //     // dataGrouping: {
        //     //     units: [[
        //     //         'week', // unit name
        //     //         [1] // allowed multiples
        //     //     ], [
        //     //         'month',
        //     //         [1, 2, 3, 4, 6]
        //     //     ]]
        //     // }
        // },
        // {
        //     type: 'column',
        //     name: 'bid-ask spread',
        //     data: spread,
        //     yAxis: 2,
        //     // dataGrouping: {
        //     //     units: [[
        //     //         'week', // unit name
        //     //         [1] // allowed multiples
        //     //     ], [
        //     //         'month',
        //     //         [1, 2, 3, 4, 6]
        //     //     ]]
        //     // }
        // }
        ]
    });
	}
  getData(f1);
});
</script>

<script type="text/javascript">
Highcharts.theme = {
   colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
      '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
   chart: {
      backgroundColor: {
         linearGradient: { x1: 1, y1: 1, x2: 1, y2: 1 },
         stops: [
            [0, '#2a2a2b'],
            [1, '#3e3e40']
         ]
      },
      style: {
         fontFamily: '\'Unica One\', sans-serif'
      },
      plotBorderColor: '#606063'
   },
   title: {
      style: {
         color: '#E0E0E3',
         textTransform: 'uppercase',
         fontSize: '20px'
      }
   },
   subtitle: {
      style: {
         color: '#E0E0E3',
         textTransform: 'uppercase'
      }
   },
   xAxis: {
      gridLineColor: '#707073',
      labels: {
         style: {
            color: '#E0E0E3'
         }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      title: {
         style: {
            color: '#A0A0A3'

         }
      }
   },
   yAxis: {
      gridLineColor: '#707073',
      labels: {
         style: {
            color: '#E0E0E3'
         }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      tickWidth: 1,
      title: {
         style: {
            color: '#A0A0A3'
         }
      },
   },
   tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.85)',
      style: {
         color: '#F0F0F0'
      }
   },
   plotOptions: {
      series: {
         dataLabels: {
            color: '#B0B0B3'
         },
         marker: {
            lineColor: '#333'
         }
      },
      boxplot: {
         fillColor: '#505053'
      },
      candlestick: {
         lineColor: 'white'
      },
      errorbar: {
         color: 'white'
      }
   },
   legend: {
      itemStyle: {
         color: '#E0E0E3'
      },
      itemHoverStyle: {
         color: '#FFF'
      },
      itemHiddenStyle: {
         color: '#606063'
      }
   },
   credits: {
      style: {
         color: '#666'
      }
   },
   labels: {
      style: {
         color: '#707073'
      }
   },

   drilldown: {
      activeAxisLabelStyle: {
         color: '#F0F0F3'
      },
      activeDataLabelStyle: {
         color: '#F0F0F3'
      }
   },

   navigation: {
      buttonOptions: {
         symbolStroke: '#DDDDDD',
         theme: {
            fill: '#505053'
         }
      }
   },

   // scroll charts
   rangeSelector: {
      buttonTheme: {
         fill: '#505053',
         stroke: '#000000',
         style: {
            color: '#CCC'
         },
         states: {
            hover: {
               fill: '#707073',
               stroke: '#000000',
               style: {
                  color: 'white'
               }
            },
            select: {
               fill: '#000003',
               stroke: '#000000',
               style: {
                  color: 'white'
               }
            }
         }
      },
      inputBoxBorderColor: '#505053',
      inputStyle: {
         backgroundColor: '#333',
         color: 'silver'
      },
      labelStyle: {
         color: 'silver'
      }
   },

   navigator: {
      handles: {
         backgroundColor: '#666',
         borderColor: '#AAA'
      },
      outlineColor: '#CCC',
      maskFill: 'rgba(255,255,255,0.1)',
      series: {
         color: '#7798BF',
         lineColor: '#A6C7ED'
      },
      xAxis: {
         gridLineColor: '#505053'
      }
   },

   scrollbar: {
      barBackgroundColor: '#808083',
      barBorderColor: '#808083',
      buttonArrowColor: '#CCC',
      buttonBackgroundColor: '#606063',
      buttonBorderColor: '#606063',
      rifleColor: '#FFF',
      trackBackgroundColor: '#404043',
      trackBorderColor: '#404043'
   },

   // special colors for some of the
   legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
   background2: '#505053',
   dataLabelsColor: '#B0B0B3',
   textColor: '#C0C0C0',
   contrastTextColor: '#F0F0F3',
   maskColor: 'rgba(255,255,255,0.3)'
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);

</script>
</html>