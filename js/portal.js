function CheckSessionAjax() {
  $.ajax({
    url: 'engine/engine.php',
    type: 'POST',
    data: {
      action: 'CheckSession'
    },
    success: function(response) {
      if (response == "true") {
        console.log(response);
      }
    },
    error: function(xhr, status, error) {
      alert(response);
    }
  });
}

$( document ).ready(function() {
  CheckSessionAjax();
});