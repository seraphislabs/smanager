function AjaxCall(data, callback) {
    $.ajax({
      url: 'engine/engine.php',
      type: 'POST',
      data: data,
      success: function(response) {
        callback(true, response);
      },
      error: function(xhr, status, error) {
        callback(false, error);
      }
    });
  }