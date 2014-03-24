$( document ).ready(function() {
$(document).ready(function(){

  $("div.sort-list-block").each(function(){
    var div = $(this);

    div.find(".sort-icon img").click(function(){
      var img = $(this);

      if(img.hasClass("asc")){
        img.removeClass("asc");
        img.attr("src", this_script_vars.imagepath + "sort-icon2.png");
        sortList(div.find(".linkbar"), true);
      }else{
        img.addClass("asc");
        img.attr("src", this_script_vars.imagepath + "sort-icon.png");
        sortList(div.find(".linkbar"));
      }
      
    });
    
  });
  
  $("div.sort-list-block-menu").each(function(){
    var div = $(this);

    div.find(".sort-icon-menu img").click(function(){
      var img = $(this);

      if(img.hasClass("asc")){
        img.removeClass("asc");
        img.attr("src", this_script_vars.imagepath + "sort-icon2-menu.png");
        sortList(div.find(".sidebar-menu"), true);
      }else{
        img.addClass("asc");
        img.attr("src", this_script_vars.imagepath + "sort-icon-menu.png");
        sortList(div.find(".sidebar-menu"));
      }
      
    });
    
  });

});

});

function sortList(list, desc) {
  var mylist = list,
      listitems = mylist.children('li').get();

  if (desc) {
    listitems.sort(function(a, b) {
     return $(b).text().toUpperCase().localeCompare($(a).text().toUpperCase());
    });
  } else {
    listitems.sort(function(a, b) {
     return $(a).text().toUpperCase().localeCompare($(b).text().toUpperCase());
    });
  }
  
  $.each(listitems, function(idx, itm) { mylist.append(itm); });
}