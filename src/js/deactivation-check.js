window.onload=function(){
  let is_used = deactivation_check.used_by;
  let element = document.getElementById("deactivate-reusable-admin-panel");

  // Check that no plugins are using Reusable Admin Panel before allowing deactivation
  if (is_used) {
    element.addEventListener("click", function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Wait!',
        html: '<strong>Reusable Admin Panel</strong> is a dependency for the following plugins:<br /><br /><strong>' + is_used + '</strong>' + ' <br /><br />You will need to make sure these plugins are deactivated first.',
        icon: 'error',
        confirmButtonColor: '#2271B1',
        iconColor: '#D63638',
      })
    });
  }
}