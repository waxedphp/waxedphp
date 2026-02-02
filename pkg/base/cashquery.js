

cash.json2query = function(o) {
  var q = '';
  if (o) {
    for (var key in o) {
      if (o.hasOwnProperty(key)) {
        q += encodeURIComponent(key) + '=' + encodeURIComponent(o[key]) +'&';
      }
    }
  }
  return q;
}

cash.get = function(url,options,callback) {
  var xhr = new XMLHttpRequest();
  if (options) {
    url += '?';
    url += $.json2query(options);
  }
  xhr.open('GET',url);
  xhr.onload = function() {
    if (xhr.status === 200) {
      callback(xhr.responseText);
    }
  }
  xhr.send();
}

cash.post = function(url,options,callback) {
  var xhr = new XMLHttpRequest();
  var data = '';
  if (options) {
    data = $.json2query(options);
  }
  xhr.open('POST',url);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    if (xhr.status === 200) {
      callback(xhr.responseText);
    }
  }
  xhr.send(data);
}

console.log(window.$);
window.jQuery = window.$ = cash;
