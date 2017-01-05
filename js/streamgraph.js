
var datearray = [];
var colorrange = [];


function loadStreamGraph() {
  document.getElementById("content").innerHTML = '<div id="streamgraphchart" class="chart"></div><div id="papertitles">Publications</div><div id="streampapers"></div><div id="freezechart"></div>';
  document.getElementById('sometext').innerHTML = _ifpritxt.streamgraph;

  chart("data/streamgraph.csv");
}




function chart(csvpath) {

strokecolor = "#ffffff";

var format = d3.time.format("%m/%d/%y");

var margin = {top: 20, right: 20, bottom: 60, left: 20};
var width = 984;
var height = 400;

var tooltip = d3.select("#content")
    .append("div")
    .attr("class", "remove")
    .style("position", "absolute")
    .style("font-family", "'PT Sans Narrow', sans-serif")
    .style('font-size','18px')
    .style("z-index", "40")
    .style("visibility", "hidden")
    .style("top", "260px")
    .style("left", "55px");


var x = d3.time.scale()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height-10, 0]);

var z = d3.scale.ordinal()
    .range(colorbrewer.Set3[12]);


var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom")
    .ticks(d3.time.years);

var yAxis = d3.svg.axis()
    .scale(y);

var yAxisr = d3.svg.axis()
    .scale(y);

var stack = d3.layout.stack()
    .offset("zero")
    .values(function(d) { return d.values; })
    .x(function(d) { return d.date; })
    .y(function(d) { return d.value; });

var nest = d3.nest()
    .key(function(d) { return d.key; });

var area = d3.svg.area()
    .interpolate("cardinal")
    .x(function(d) { return x(d.date); })
    .y0(function(d) { return y(d.y0); })
    .y1(function(d) { return y(d.y0 + d.y); });

var svg = d3.select(".chart").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var graph = d3.csv(csvpath, function(data) {
  data.forEach(function(d) {
    d.date = format.parse(d.date);
    d.value = +d.value;
  });

// Freeze chart when someone clicks on something
d3.select(".chart").on("click", function() {
  d3.select("#freezechart").style("visibility", "visible");
});

d3.select("#freezechart").on("mouseleave", function() {
  d3.select("#freezechart").style("visibility", "hidden");
});

  var layers = stack(nest.entries(data));

  x.domain(d3.extent(data, function(d) { return d.date; }));
  y.domain([0, d3.max(data, function(d) { return d.y0 + d.y; })]);

  svg.selectAll(".layer")
      .data(layers)
    .enter().append("path")
      .attr("class", "layer")
      .attr("d", function(d) { return area(d.values); })
      .style("fill", function(d, i) { return z(i); });


  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  /* svg.append("g")
      .attr("class", "y axis")
      .attr("transform", "translate(" + width + ", 0)")
      .call(yAxis.orient("right")); 

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis.orient("left")); */

  svg.selectAll(".layer")
    .attr("opacity", 1)
    .on("mouseover", function(d, i) {
      svg.selectAll(".layer").transition()
      .duration(250)
      .attr("opacity", function(d, j) {
        return j != i ? 0.6 : 1;
    })})

    .on("mousemove", function(d, i) {
      mousex = d3.mouse(this);
      mousex = mousex[0];
      var invertedx = x.invert(mousex);
      invertedx = invertedx.getYear();
      var selected = (d.values);
      for (var k = 0; k < selected.length; k++) {
        datearray[k] = selected[k].date
        datearray[k] = datearray[k].getYear();
      }

      mousedate = datearray.indexOf(invertedx) + 1;
      pro = d.values[mousedate].value;
      papers = getStreamPapers(mousedate, d.key);

      document.getElementById("streampapers").innerHTML = papers;

      d3.select(this)
      .classed("hover", true)
      .attr("stroke", strokecolor)
      .attr("stroke-width", "0.5px"), 
      tooltip.html( "<p><b>" + d.key + "</b>:" + pro + "</p>" ).style("visibility", "visible");
      
    })
    .on("mouseout", function(d, i) {
     svg.selectAll(".layer")
      .transition()
      .duration(250)
      .attr("opacity", "1");
      d3.select(this)
      .classed("hover", false)
      .attr("stroke-width", "0px"), tooltip.html( "<p><b>" + d.key + "</b>:" + pro + "</p>" ).style("visibility", "hidden");
  })
    
  var vertical = d3.select(".chart")
        .append("div")
        .attr("class", "remove")
        .style("position", "absolute")
        .style("z-index", "19")
        .style("width", "1px")
        .style("height", "380px")
        .style("top", "10px")
        .style("bottom", "30px")
        .style("left", "0px")
        .style("background", "#fff");

  d3.select(".chart")
      .on("mousemove", function(){  
         mousex = d3.mouse(this);
         mousex = mousex[0] + 5;
       })
         //vertical.style("left", mousex + "px" )})
      .on("mouseover", function(){  
         mousex = d3.mouse(this);
         mousex = mousex[0] + 5;
       })
         //vertical.style("left", mousex + "px")});
});

}

function getStreamPapers(date, keyword) {
  var content = "<ul>";
  var date = parseInt((date > 9) ? ("20" + date) : ("200" + date));
  for(var i in _streamdata[keyword]) {
    if (_streamdata[keyword][i].year == (date))
      content += "<li style='margin: 4px 0;'>" + _streamdata[keyword][i].authors.trim() + " ("+_streamdata[keyword][i].year.trim()+") <i>" + _streamdata[keyword][i].title.trim() + "</i>. " + _streamdata[keyword][i].journal.trim() + ". [<a target='_blank' href='http://doi.org/"+_streamdata[keyword][i].doi+"'>"+_streamdata[keyword][i].doi+"</a>]</li>";
  }
  return content + "</ul>";
}