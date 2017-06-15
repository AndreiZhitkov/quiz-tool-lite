<script>
var defaultTab;
jQuery("document").ready(function()
{
	defaultTab = (parseInt(getParam('tab'))-1);
    jQuery( "#tabs" ).tabs({active: defaultTab});
});
</script>
