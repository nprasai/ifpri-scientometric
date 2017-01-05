var _mpr = new Array();
var _mpr2 = new Array();
var _chordcolors = new Array();
var _streamdata = null;
var _articlecounts = null;
var _articlecountscrp = null;

loadHome();
loadData();
loadStreamData();

function loadData() {
  d3.csv('data/chords/colors2.csv', function (error, data) {
    for(var d in data) {
      _chordcolors[data[d].country] = data[d].color;
    }
  });

  d3.json('data/chords/articlecounts.json', function (error, data) {
    _articlecounts = data;
  });

  d3.json('data/chords/articlecountscrp.json', function (error, data) {
    _articlecountscrp = data;
  });

  d3.csv('data/chords/crp2016.csv', function (error, data) {
    _mpr2[2016] = chordMpr(data);

    _mpr2[2016]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/crp2009.csv', function (error, data) {
    _mpr2[2009] = chordMpr(data);

    _mpr2[2009]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/crp2004.csv', function (error, data) {
    _mpr2[2004] = chordMpr(data);

    _mpr2[2004]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });


  d3.csv('data/chords/orgs2016.csv', function (error, data) {
    _mpr[2016] = chordMpr(data);

    _mpr[2016]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
    //drawChords(_mpr[2016].getMatrix(), _mpr[2016].getMap());
  });

  d3.csv('data/chords/orgs2015.csv', function (error, data) {
    _mpr[2015] = chordMpr(data);

    _mpr[2015]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
    //drawChords(mpr.getMatrix(), mpr.getMap());
  });

  d3.csv('data/chords/orgs2014.csv', function (error, data) {
    _mpr[2014] = chordMpr(data);

    _mpr[2014]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2013.csv', function (error, data) {
    _mpr[2013] = chordMpr(data);

    _mpr[2013]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2012.csv', function (error, data) {
    _mpr[2012] = chordMpr(data);

    _mpr[2012]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2011.csv', function (error, data) {
    _mpr[2011] = chordMpr(data);

    _mpr[2011]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2010.csv', function (error, data) {
    _mpr[2010] = chordMpr(data);

    _mpr[2010]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2009.csv', function (error, data) {
    _mpr[2009] = chordMpr(data);

    _mpr[2009]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2008.csv', function (error, data) {
    _mpr[2008] = chordMpr(data);

    _mpr[2008]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2007.csv', function (error, data) {
    _mpr[2007] = chordMpr(data);

    _mpr[2007]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2006.csv', function (error, data) {
    _mpr[2006] = chordMpr(data);

    _mpr[2006]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2005.csv', function (error, data) {
    _mpr[2005] = chordMpr(data);

    _mpr[2005]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2004.csv', function (error, data) {
    _mpr[2004] = chordMpr(data);

    _mpr[2004]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2003.csv', function (error, data) {
    _mpr[2003] = chordMpr(data);

    _mpr[2003]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2002.csv', function (error, data) {
    _mpr[2002] = chordMpr(data);

    _mpr[2002]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2001.csv', function (error, data) {
    _mpr[2001] = chordMpr(data);

    _mpr[2001]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });

  d3.csv('data/chords/orgs2000.csv', function (error, data) {
    _mpr[2000] = chordMpr(data);

    _mpr[2000]
      .addValuesToMap('who')
      .setFilter(function (row, a, b) {
        return (row.who === a.name && row.overlap === b.name)
      })
      .setAccessor(function (recs, a, b) {
        if (!recs[0]) return 0;
        return +recs[0].years;
      });
  });
}


function loadStreamData() {
    d3.json('data/stream/articles.json', function(error, data) {
        _streamdata = data;
    });
}