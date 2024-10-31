jQuery.extend({
        postsectionsrequest:function(url,data,success){
            jQuery.ajax({
              type: 'POST',
              url: url,
              data: data,
              success: success,
              async:false
            });
        },
        getSectionParams:function(direction){
            var result={};
            var parameters = direction.split('?');
            result['url']=parameters[0];
            para=parameters[1].split('&');
            for(var i = 0;  i < para.length; i++) {
                    var parameter = para[i].split('=');
                    result[parameter[0]] = parameter[1];
            }
            return result;
        },
        getSection: function(section,direction){
            return jQuery.getSectionParams(direction)[section];
        },
        addNavigation:function(tags,postid){
            jQuery(tags).click(function(event){
                event.preventDefault();
                var link=event.currentTarget.toString();
                var sections=jQuery.getSectionParams(link);
                jQuery.postsectionsrequest(window.postsectionsurl,{section:sections['section'],wppost:sections['wppost'],wppostsections:sections['wppostsections'],action:'postSection'},function(result){
                jQuery(postid).html(result);window.psloc=sections['url']+"?section="+sections['section']+"%26wppost="+sections['wppost']+"\";";
                    window.pstitle="Part "+sections['section']+" of "+sections['wppost'];
                jQuery.addNavigation(tags,postid);
                jQuery.addBookmark('a#postsectionbookmark',window.psloc,window.pstitle);
                });
            });
        },
        addBookmark:function(tag,loc,title){
            jQuery(tag).click(function(event){
                event.preventDefault();
                var link=event.currentTarget.toString();
                var sections=jQuery.getSectionParams(link);
                var bkmk=loc.replace("%26","&");
                jQuery.post(window.postsectionsurl,{section:sections['section'],wppost:sections['wppost'],action:'bookmark'},function(result){
                   window.alert(result);
                });
                if(document.all){
                    window.external.AddFavorite(bkmk,title);
                }else{
                    if(window.sidebar){
                        window.sidebar.addPanel(title,bkmk,'');
                    }else{
                        if (window.opera){
                            var elem = document.createElement('a');
                            elem.setAttribute('href',bkmk);
                            elem.setAttribute('title',title);
                            elem.setAttribute('rel','sidebar');
                            elem.click();
                        }else{window.alert("Your browser does not support this function.  Right click on the link and add to favourites to return to this section.  Thank you")}
                    }
                }
                jQuery.addBookmark(tag,loc,title);
            });
        }
});
jQuery('document').ready(function(){
    jQuery.addNavigation(window.postsectiontags,window.postsectiondiv);
    jQuery.addBookmark('a#postsectionbookmark',window.psloc,window.pstitle);
});