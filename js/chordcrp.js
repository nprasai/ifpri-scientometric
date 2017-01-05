      //*******************************************************************
      //  CREATE MATRIX AND MAP
      //*******************************************************************
      //loadChord();
      var _slider = null;
      var _color = new Array();

      function loadChordCRP() {
        //d3.select("#content").remove();
        document.getElementById("content").innerHTML = "<img id='loading' style='margin-top:30px;' src='img/ripple.gif'/>";
        setTimeout(loadChordCRP2, 1000);

      }
      function loadChordCRP2() {
        d3.select("#loading").remove();
        
        d3.select("#content").append("div").attr('id','sliderttext2');
        d3.select("#content").append("div").attr('id','timeslider2');
        document.getElementById('sliderttext2').innerHTML = "CRP Phase 1";
        document.getElementById('sometext').innerHTML = _ifpritxt.collaborationscrp;

        document.getElementById('timeslider2').innerHTML = "<div class='btncrp' onclick='changecrp(1)'>Pre-CRP (2000-2004)</div><div class='btncrp' onclick='changecrp(2)'>Pre-CRP (2005-2009)</div><div class='btncrp' style='padding:20px 20px' onclick='changecrp(3)'>CRP Phase 1</div>";

        drawChordsCRP(_mpr2[2016].getMatrix(), _mpr2[2016].getMap());
      }

      function changecrp(id) {
        var content = "";
        if (id == 3) {
          content = "CRP Phase 1";
          drawChordsCRP(_mpr2[2016].getMatrix(), _mpr2[2016].getMap());
        } else if (id == 2) {
          content = "Pre-CRP (2005-2009)";
          drawChordsCRP(_mpr2[2009].getMatrix(), _mpr2[2009].getMap());
        } else {
          content = "Pre-CRP (2000-2004)";
          drawChordsCRP(_mpr2[2004].getMatrix(), _mpr2[2004].getMap());
        }
        document.getElementById('sliderttext2').innerHTML = content;
      }

      //*******************************************************************
      //  DRAW THE CHORD DIAGRAM
      //*******************************************************************
      function drawChordsCRP (matrix, mmap) {
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
            .attr("font-size","10px")
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
            var a = document.getElementById('sliderttext2').innerHTML;
            var b = "";
            if (a == "Pre-CRP (2000-2004)")
              b = 2004;
            else if (a == "Pre-CRP (2005-2009)")
              b = 2009;
            else
              b = 2016;
            return d.gname + "("+_articlecountscrp[b][d.gname]+" publications)";
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