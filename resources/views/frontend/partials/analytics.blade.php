@if (env("GOOGLE_ANALYTICS_STATUS"))
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{env("GOOGLE_ANALYTICS_ID")}}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-GTRVREE0F4');
</script>
@endif
