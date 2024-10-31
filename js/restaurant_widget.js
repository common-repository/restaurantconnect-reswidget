 function iframeLoaded() {
      var iFrameID = document.getElementById('idIframe');

      if(iFrameID) {
           iFrameID.height = "";

		  if(iFrameID.contentWindow.document.body.scrollHeight < 425){
			  iFrameID.height = "425px";
		  } else {
			  iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
		  }

      }   
  }

jQuery( document ).ready(function() {
  var myIframe = document.getElementById('restaurantconnect');
  myIframe.contentWindow.addEventListener('click', getClick);

  window.addEventListener('message', function(e) {
	  var $iframe = jQuery("#restaurantconnect");
	  var eventName = e.data[0];
	  var data = e.data[1] + 10;
	  if(data < 425){
		  data = 425;
	  }
	  switch(eventName) {
		  case 'setHeight':
			  $iframe.animate({height:data},200);
			  break;
	  }
  }, false);
});

  

function getClick(){
  document.getElementById('restaurantconnect').contentWindow.resizeIframe();

}
function getResize(){
  document.getElementById('restaurantconnect').contentWindow.resizeIframe();

}
function resizeIframe() {
  var newHeight = $('#restaurantconnect').contents().height();

  if(newHeight < 425){
	  newHeight = 425;

  }
 $('#restaurantconnect').height();
 $('#restaurantconnect').animate({height:newHeight},500);

}

