<?php
if (is_user_logged_in ()):
  // get the plugin base url
  $pluginRoot = plugins_url('', dirname( __FILE__ ));
  $sec = new SpotifyEmbedCreator();
  wp_enqueue_script('SpotifyEmbedCreator');
?>
  <div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>
    <h2>Spotify Embed Creator</h2>
  </div>
  <div id="spotify-container">
    <table class="wp-list-table widefat users" cellspacing="0">
      <thead>
        <tr>
          <th scope="col" id="role" class="manage-column column-role" style="display:none;">Search Artist</th>
          <th scope="col" id="role" class="manage-column column-role" style="">Search Album</th>
          <th scope="col" id="role" class="manage-column column-role" style="">Search Track</th>
          
          <th scope="col" id="role" class="manage-column column-role" style="">Width</th>
          <th scope="col" id="role" class="manage-column column-role" style="">Height</th>          
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="col" id="role" class="manage-column column-role" style="display:none;">
          	<input type="text" id="artist-search" />
          	<input type="button" id="artist-do-search" value="Search" />
          </th>
          <th scope="col" id="role" class="manage-column column-role" style="">
          	<input type="text" id="album-search" />          
          	<input type="button" id="album-do-search" value="Search" />          	
          </th>
          <th scope="col" id="role" class="manage-column column-role" style="">
          	<input type="text" id="song-search" />          
          	<input type="button" id="song-do-search" value="Search" />          	
          </th>
          
          <th scope="col" id="role" class="manage-column column-role" style="">
          	<input type="text" id="iframe-width" value="300" />             
    		  </th>
          <th scope="col" id="role" class="manage-column column-role" style="">
            <!-- <input type="text" id="iframe-height" value="380" /> -->
    				<label>Compact</label>
    				<input type="checkbox"id="compact" />
    		  </th>        
        </tr>  
      </tbody>
    </table>
  </div>

  <div id="spotify-result-container" style="float:left;width:50%;padding-right:20px;">
  </div>
  <div style="float:left; width: 47%;">
	  <div id="codeboxes" style="display:none;">
		  <h3>iFrame code</h3>
		  <textarea id="iframe-code" cols="100" rows="4"></textarea>
		  <h3>Shortcode</h3>
		  <textarea id="shortcode-code" cols="100" rows="4"></textarea>  
  	  </div>  
	  <div id="spotify-preview-container" style="float:left;"></div>  
  </div>

  <div class="clear"></div>
  <script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery('#artist-do-search').bind('click', function(event) {
      	var query = jQuery('#artist-search').val();
      	console.log("Ska söka artist: " + query);
      	search_spotify("artist", query, "");
      });
      jQuery('#album-do-search').bind('click', function(event) {
      	var query = jQuery('#album-search').val();
      	console.log("Ska söka album: " + query);
      	search_spotify("album", query, "");
      });
      jQuery('#song-do-search').bind('click', function(event) {
      	var query = jQuery('#song-search').val();
      	console.log("Ska söka låt: " + query);
      	search_spotify("track", query, "");
      });    
    });  
    function search_spotify(type, query, path)
    {
    	jQuery("#spotify-result-container").html("Searching...");
    	jQuery("#spotify-preview-container").hide();
    	jQuery("#codeboxes").hide();
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo $pluginRoot ?>" + path + "/Api/Spotify-Request-handler.php",
                    async: true,
                    timeout: 50000,
                    data: { searchtype: type, searchquery: query.replace(/ /g,"+") },
                    success: function(data) {
                    	console.log("lyckades");
                    	var html = '<ul class="result-list">';
                    	if(type == "artist")
                    	{
                    		for(var i = 0; i < data.artists.length; ++i)
                    		{
                    			//html += '<li>'+ data.artists[i].name +' - <input type="button" onClick="get_iframe_code(\''+data.artists[i].href+'\');" value="Skapa iframe"/></li>';
                    		}
                    	}
                    	if(type == "album")
                    	{
                    		for(var i = 0; i < data.albums.length; ++i)
                    		{
                    			//html += '<li>'+ data.albums[i].artists[0].name + ' - ' + data.albums[i].name +' - <input type="button" onClick="get_iframe_code(\''+data.albums[i].href+'\');" value="Skapa iframe"/></li>';
                    			html += '<li><a href="#" onClick="get_iframe_code(\''+data.albums[i].href+'\');">'+ data.albums[i].artists[0].name + ' - ' + data.albums[i].name +'</a></li>';
                    		}                	
                    	}
                    	if(type == "track")
                    	{
                    		for(var i = 0; i < data.tracks.length; ++i)
                    		{
                    			//html += '<li>'+ data.tracks[i].artists[0].name + ' - ' + data.tracks[i].name +' - <input type="button" onclick="get_iframe_code(\''+data.tracks[i].href+'\');" value="Skapa iframe"/></li>';
                    			html += '<li><a href="#" onclick="get_iframe_code(\''+data.tracks[i].href+'\');">' + data.tracks[i].artists[0].name + ' - ' + data.tracks[i].name +'</a></li>';
                    		}                	
                    	}
                    	html += '</ul>';
                        jQuery("#spotify-result-container").html(html);
                    },
                    error: function(data) {
					  if(data.status == 404)
					  {
						alert("Something went wrong with the url (seems to happend on Binero)");
					  }
					  else
					  {
						console.log("misslyckades");
						alert("Something went wrong");
					  }
                    }
                });
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo $pluginRoot ?>" + path + "/Api/Spotify-Request-handler.php",
                    async: true,
                    timeout: 50000,
                    data: { searchtype: type, searchquery: query },
                    success: function(data) {
                    	console.log("lyckades");
                    	var html = '<ul class="result-list">';
                    	if(type == "artist")
                    	{
                    		for(var i = 0; i < data.artists.length; ++i)
                    		{
                    			//html += '<li>'+ data.artists[i].name +' - <input type="button" onClick="get_iframe_code(\''+data.artists[i].href+'\');" value="Skapa iframe"/></li>';
                    		}
                    	}
                    	if(type == "album")
                    	{
                    		for(var i = 0; i < data.albums.length; ++i)
                    		{
                    			//html += '<li>'+ data.albums[i].artists[0].name + ' - ' + data.albums[i].name +' - <input type="button" onClick="get_iframe_code(\''+data.albums[i].href+'\');" value="Skapa iframe"/></li>';
                    			html += '<li><a href="#" onClick="get_iframe_code(\''+data.albums[i].href+'\');">'+ data.albums[i].artists[0].name + ' - ' + data.albums[i].name +'</a></li>';
                    		}                	
                    	}
                    	if(type == "track")
                    	{
                    		for(var i = 0; i < data.tracks.length; ++i)
                    		{
                    			//html += '<li>'+ data.tracks[i].artists[0].name + ' - ' + data.tracks[i].name +' - <input type="button" onclick="get_iframe_code(\''+data.tracks[i].href+'\');" value="Skapa iframe"/></li>';
                    			html += '<li><a href="#" onclick="get_iframe_code(\''+data.tracks[i].href+'\');">' + data.tracks[i].artists[0].name + ' - ' + data.tracks[i].name +'</a></li>';
                    		}                	
                    	}
                    	html += '</ul>';
                        jQuery("#spotify-result-container").append(html);
                    },
                    error: function(data) {
					  if(data.status == 404)
					  {
						alert("Something went wrong with the url (seems to happend on Binero)");
					  }
					  else
					  {
						console.log("misslyckades");
						alert("Something went wrong");
					  }
                    }
                });	 				
    }

    function get_iframe_code(href)
    {
    	var iframewidth = jQuery("#iframe-width").val();
    //	var iframeheight = jQuery("#iframe-height").val();	
    	var compact = jQuery("#compact").is(":checked") == true ? "80" : parseInt(iframewidth)+80;
    	var iframehtml = '<iframe src="https://embed.spotify.com/?uri='+href+'" width="'+ iframewidth +'" height="'+ compact +'" frameborder="0" allowtransparency="true"></iframe>';
    	var sizetype = compact == "80" ? "compact" : "width";
    	var shortcodehtml = '[spotify play="'+href+'" size="'+iframewidth+'" sizetype="'+sizetype+'"]';
    	jQuery("#iframe-code").val(iframehtml);
    	jQuery("#shortcode-code").val(shortcodehtml);
    	jQuery("#codeboxes").show();
    	jQuery("#spotify-preview-container").html(iframehtml);
    	jQuery("#spotify-preview-container").show();
    	location.href = "#spotify-container";
    }
  </script>
  <style type="text/css">
    #iframe-code {
    	width: 100%;
    	margin: 10px 0 10px 0;
    }
    #shortcode-code {
    	width: 100%;
    	margin: 10px 0 10px 0;
    }  
    .result-list li {
    	padding-bottom: 10px;
    	margin-bottom: 10px;
    	border-bottom: 1px dashed #c6c6c6;
    }
  </style>
<?php
endif;
?>