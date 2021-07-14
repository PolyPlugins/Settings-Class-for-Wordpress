var bootstrapCss = 'bootstrapCss';
if (!document.getElementById(bootstrapCss)) {
  var head = document.getElementsByTagName('head')[0];
  var bootstrapWrapper = document.createElement('link');
  bootstrapWrapper.id = bootstrapCss;
  bootstrapWrapper.rel = 'stylesheet/less';
  bootstrapWrapper.type = 'text/css';
  bootstrapWrapper.href = plugin_properties.plugin_url + '/' + plugin_properties.plugin_slug + '/css/backend/bootstrap-wrapper.less';
  bootstrapWrapper.media = 'all';
  head.appendChild(bootstrapWrapper);

  var lessjs = document.createElement('script');
  lessjs.type = 'text/javascript';
  lessjs.src = plugin_properties.plugin_url + '/' + plugin_properties.plugin_slug + '/js/backend/less.min.js';
  head.appendChild(lessjs);

  // Load custom bootstrap styles
  // var customStyles = document.createElement('link');
  // customStyles.id = "customStyles";
  // customStyles.rel = 'stylesheet';
  // customStyles.type = 'text/css';
  // customStyles.href = plugin_properties.plugin_url + '/' + plugin_properties.plugin_slug + '/css/backend/bootstrap-styles.css';
  // customStyles.media = 'all';
  // head.appendChild(customStyles);
}