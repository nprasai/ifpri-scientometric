      //*******************************************************************
      //  CREATE MATRIX AND MAP
      //*******************************************************************
      //loadChord();
      var _slider = null;
      var _color = new Array();

      function loadChord() {
        //d3.select("#content").remove();
        document.getElementById("content").innerHTML = "<img id='loading' style='margin-top:30px;' src='img/ripple.gif'/>";
        setTimeout(loadChord2, 1000);

      }
      function loadChord2() {
        d3.select("#loading").remove();
        
        d3.select("#content").append("div").attr('id','sliderttext');
        d3.select("#content").append("div").attr('id','timeslider');
        document.getElementById('sliderttext').innerHTML = "2016";
        document.getElementById('sometext').innerHTML = _ifpritxt.collaborationsyear;

        var ii = (navigator.userAgent.search("Firefox") > -1) ? false : true;

        _slider = d3.slider()
            .axis(ii)
            .orientation("vertical")
            .min(2000)
            .max(2016)
            .value(2016)
            .on("slide", function(evt, value) {
              d3.select('#sliderttext').text(value);
              drawChords(_mpr[value].getMatrix(), _mpr[value].getMap());
            });
        

        d3.select('#timeslider').call(_slider);
        _slider.value(2016);
        drawChords(_mpr[2016].getMatrix(), _mpr[2016].getMap());
      }

      //*******************************************************************
      //  DRAW THE CHORD DIAGRAM
      //*******************************************************************
      function drawChords (matrix, mmap) {
        _color = new Array();
        for(var i in mmap) {
            _color.push(_chordcolors[mmap[i].name]);
        }
        var w = 900, h = 900, r1 = h / 2, r0 = r1 - 150;
        d3.select("#diagram").remove();
        d3.select("#content").append("div").attr('id','diagram');
        // var fill = d3.scale.category10();

        var chord = d3.layout.chord()
            .padding(.02)
            .sortSubgroups(d3.descending)
            .sortChords(d3.descending);

        var arc = d3.svg.arc()
            .innerRadius(r0)
            .outerRadius(r0 + 20);

        var svg = d3.select("#diagram").append("svg")
            .attr("width", w)
            .attr("height", h)
          .append("svg:g")
            .attr("id", "circle")
            .attr("transform", "translate(" + w / 2 + "," + h / 2 + ")");

            svg.append("circle")
                .attr("r", r0 + 20);

        var rdr = chordRdr(matrix, mmap);
        chord.matrix(matrix);

        var g = svg.selectAll("g.group")
            .data(chord.groups())
          .enter().append("svg:g")
            .attr("class", "group")
            .on("mouseover", mouseover)
            .on("mouseout", function (d) { d3.select("#tooltip").style("visibility", "hidden") });

        g.append("svg:path")
            .style("stroke", "none")
            .style("fill", function(d) { 
              return _color[d.index]; 
            })
            .attr("d", arc);

        g.append("svg:text")
            .each(function(d) { d.angle = (d.startAngle + d.endAngle) / 2; })
            .attr("dy", ".35em")
            .attr("text-anchor", function(d) { return d.angle > Math.PI ? "end" : null; })
            .attr("transform", function(d) {
              return "rotate(" + (d.angle * 180 / Math.PI - 90) + ")"
                  + "translate(" + (r0 + 26) + ")"
                  + (d.angle > Math.PI ? "rotate(180)" : "");
            })
            .text(function(d) { return rdr(d).gname; });

          var chordPaths = svg.selectAll("path.chord")
                .data(chord.chords())
              .enter().append("svg:path")
                .attr("class", "chord")
                .style("stroke", function(d) { return d3.rgb(_color[d.target.index]).darker(); })
                .style("fill", function(d) { return _color[d.target.index]; })
                .attr("d", d3.svg.chord().radius(r0))
                .on("mouseover", function (d) {
                  d3.select("#tooltip")
                    .style("visibility", "visible")
                    .html(chordTip(rdr(d)))
                    .style("top", function () { return (d3.event.pageY - 100)+"px"})
                    .style("left", function () { return (d3.event.pageX - 100)+"px";})
                })
                .on("mouseout", function (d) { d3.select("#tooltip").style("visibility", "hidden") });

          function chordTip (d) {
            var p = d3.format(".0%"), q = d3.format("0d")
            var content = "";
            if (d.sname != d.tname)
              content = q(d.svalue) + " publications with coauthors at " + d.sname + " and " + d.tname;
            else
              content = q(d.svalue) + " "+d.tname+" single institution publications"; 
            return content;
          }

          function groupTip (d) {
            var a = document.getElementById('sliderttext').innerHTML;
            return d.gname + "("+_articlecounts[a][d.gname]+" publications)";
          }

          function mouseover(d, i) {
            d3.select("#tooltip")
              .style("visibility", "visible")
              .html(groupTip(rdr(d)))
              .style("top", function () { return (d3.event.pageY - 80)+"px"})
              .style("left", function () { return (d3.event.pageX - 130)+"px";})

            chordPaths.classed("fade", function(p) {
              return p.source.index != i
                  && p.target.index != i;
            });
          }
      }