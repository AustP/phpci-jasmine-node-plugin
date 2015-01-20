var jasmineNodePlugin = ActiveBuild.UiPlugin.extend({
  id: 'build-jasmine-node-errors',
  css: 'col-lg-6 col-md-12 col-sm-12 col-xs-12',
  title: 'Jasmine-Node',
  lastData: null,
  displayOnUpdate: false,
  box: true,
  rendered: false,

  register: function() {
    var self = this;
    var query = ActiveBuild.registerQuery('jasmine-node-data', -1, {key: 'jasmine-node-data'});

    $(window).on('jasmine-node-data', function(data) {
      self.onUpdate(data);
    });

    $(window).on('build-updated', function() {
      if (!self.rendered) {
        self.displayOnUpdate = true;
        query();
      }
    });
  },

  render: function() {
    return $('<div id="jasmine-node-metadata" style="padding: 0 10px">' + Lang.get('pending') +
    '</div><table class="table" id="jasmine-node-data">' +
    '<thead>' +
    '<tr>' +
    '<th></th>' +
    '</tr>' +
    '</thead><tbody></tbody></table>');
  },

  onUpdate: function(e) {
    if (!e.queryData) {
      $('#build-jasmine-node-errors').hide();
      return;
    }

    this.rendered = true;
    this.lastData = e.queryData;

    var tests = this.lastData[0].meta_value;
    var tbody = $('#jasmine-node-data tbody');
    tbody.empty();

    $('#jasmine-node-metadata').html(tests.metadata.seconds + '<br>' + tests.metadata.specData);

    for (var i=0, l=tests.expectations.length; i<l; i++) {
      var expectation = tests.expectations[i];
      var html = '<td><b>' + expectation.d + '</b><br>' +
      expectation.e + '<br>' +
      '<em>' + expectation.s.replace("\n", "<br>") + '</em></td>';

      var tr = document.createElement('tr');
      tr.className = 'danger';
      tr.innerHTML = html;
      tbody.append(tr);
    }

    $('#build-jasmine-node-errors').show();
  }
});

ActiveBuild.registerPlugin(new jasmineNodePlugin());
