// logging
window.log = function(){
  log.history = log.history || [];   
  log.history.push(arguments);
  if(this.console){
    console.log( Array.prototype.slice.call(arguments) );
  }
};

// init Lawnchair to have local storage. would love to use local storage, but fluidapp does not support it, thus using cookies.
//var app = new Lawnchair({adaptor: 'webkit'});
var app = new Lawnchair({adaptor: 'cookie'});

var favoriteAppsValues = [];
var favoriteAppsTemplate = "{{#notEmpty}}<ul>{{#item}}<li><a href=\"#\" id=\"{{appId}}\" accesskey={{key}}><img src=\"https://d2cmuesa4snpwn.cloudfront.net/static/icons/{{appIcon}}\" width=\"24\" height=\"24\" alt=\"{{appName}}\"><span class=\"appName\">{{appName}}</span><span class=\"spaceName\">{{spaceName}}</span></a></li>{{/item}}</ul>{{/notEmpty}}";

var autocompleteApps = {
  labels: [],
  ids: new Array()
}

var resizeApp = function() {
  if (navigator.userAgent.match(/Fluid/i)) {
    window.resizeTo(508, 500);
  };
}

// get local stored favorite apps and show them
var getFavoriteApps = function () {
  $("#favedApps ul").remove();
  $("#favedApps").html('');
  favoriteAppsValues = [];
  
  // reset document.keydown
  $(document).unbind('keydown');
  $(document).keydown(function (e) {
    //log(e.keyCode);
    if (e.keyCode == 83 && e.altKey) { // pressing s will set focus to global search field unless focus in in any other form field
      $("#search").focus();
      return false;
    };
  });
  
  app.get("favoriteApps", function(r){
    if (r != null && r.values.length > 0) {
      for (var i = r.values.length - 1; i >= 0; i--){
        favoriteAppsValues.push(r.values[i]);
      };
    };
    
    var favoriteAppsData = {
      item: favoriteAppsValues,
      notEmpty: function() {
        if (this.item.length > 0) {return true;} return false;
      }
    };
    
    if (favoriteAppsValues.length == 0) {
      $("#favedApps").html('You have no favorite apps so far. Please use the search ^ to open an app.');
    };
    
    // show favoriteApps
    var html = Mustache.to_html(favoriteAppsTemplate, favoriteAppsData);
    $("#favedApps").append(html);
    $("#favedApps a").each(function(index, element) {
      
      // add hotkey for each app. just 1 till n
      $(document).keydown(function (e) {
        
        //if(e.keyCode == index + 49 && focusNotInForm()) { // start at keyCode 49, which is 1
        if(e.keyCode == index + 49 && e.altKey) { // start at keyCode 49, which is 1
          loadAppForm($(element).attr("id"));
        }
      });
      
      // add click handler
      $(this).click(function(event) {
        event.preventDefault();
        loadAppForm($(this).attr("id"));
      }).attr("accessKey", index+1);
    });
    
  });
}

var registerActionFavApp = function () {
  $("#favAppIcon").html('<a href="#" title="Add this application to favorites" id="favApp"><img src="/images/star.png" width="16" height="16" alt="add"></a>');
  $("#favApp").click(function(event) {
    event.preventDefault();
    favApp();
  });
}

var registeActionUnFavApp = function () {
  $("#favAppIcon").html('<a href="#" title="Remove this application from your favorites" id="unFavApp"><img src="/images/unstar.png" width="16" height="16" alt="remove"></a>');
  $("#unFavApp").click(function(event) {
    event.preventDefault();
    unFavApp();
  });
}

var loadAppForm = function (appId) {
  $("#main").html("Loading " + $('#' + appId + ' .appName').html() + ' app on ' + $('#' + appId + ' .spaceName').html() + '...');
  $.get('actions/item_new.php', { appId: appId }, function(data) {
    $("#main").html(data);
    var favedApp = false;
    for (var i=0; i < favoriteAppsValues.length; i++) {
      if (favoriteAppsValues[i].appId == appId) {
        favedApp = true;
        break;
      }
    }
    if (favedApp) {
      registeActionUnFavApp();
    } else {
      registerActionFavApp();
    }
  });
}

// get all spaces and apps that belong to the current user
var getSpacesAndApps = function () {
  $.getJSON('actions/get_spaces_and_apps.php', function(data) {
    $.each(data, function() {
      var org = this;
      $.each(org.spaces, function() {
        var space = this;
        $.each(space.apps, function() {
          autocompleteApps.labels.push(this.config.name + ' > ' + space.name + ' > ' + org.name);
          autocompleteApps.ids[this.config.name + ' > ' + space.name + ' > ' + org.name] = this.app_id;
        });
      });
    });
    $("#search").autocomplete({
      source: autocompleteApps.labels,
      select: function(event, ui) {
        loadAppForm(autocompleteApps.ids[ui.item.label]);
      }
    }).attr('placeholder', 'search for an app within spaces and organisations').removeAttr('disabled');
  });
}

var focusNotInForm = function () {
  if ($("input:focus").length == 0 && $("select:focus").length == 0 && $("button:focus").length == 0 && $("textarea:focus")) {
    return true;
  } else {
    return false;
  }
}

var favApp = function () {
  // getinfos from current shown app form
  var appForm = $("#item_new_form");
  var appId = $("#appId", appForm).val();
  
  // check if app is already faved
  if (favoriteAppsValues.length > 0) {
    for (var i = favoriteAppsValues.length - 1; i >= 0; i--){
      if (favoriteAppsValues[i].appId == appId) {
        return;
      };
    };
  }
  
  favoriteAppsValues.push({
    appId: appId,
    appName: $("#appName", appForm).val(),
    appIcon: $("#appIcon", appForm).val(),
    spaceName: $("#spaceName", appForm).val(),
    spaceUrl: $("#spaceUrl", appForm).val()
  });
  
  app.save({key:'favoriteApps', values: favoriteAppsValues});
  getFavoriteApps();
  registeActionUnFavApp();
}

var unFavApp = function () {
  // getting infos from current shown app form
  var appForm = $("#item_new_form");
  var appId = $("#appId", appForm).val();
  
  // check if app is already faved
  if (favoriteAppsValues.length > 0) {
    for (var i = favoriteAppsValues.length - 1; i >= 0; i--){
      if (favoriteAppsValues[i].appId == appId) {
        favoriteAppsValues.splice(i,1);
      };
    };
  }
  
  app.save({key:'favoriteApps', values: favoriteAppsValues});
  getFavoriteApps();
  registerActionFavApp();
}


// validates a form by checking if any required fields are not filled out. triggeres the validation on each form field keyup and change
// form can not be sent if not all required fields have a value, also changes state of "submit" element
var validateForm = function (element) {
  
  if ($(element).val().length > 0) {
    $(element).removeClass("required");
  } else {
    $(element).addClass("required");
  }
  
  if ($("#item_new_form .required").length <= 0) {
    $("#item_new_form input[type=submit]").removeAttr('disabled');
  } else {
    $("#item_new_form input[type=submit]").attr('disabled', 'disabled');
  }
};

/**
 * checks if unread notifications are on podio and alerts the user
 */
var showUnreadNotifications = function () {
  $.getJSON('/actions/get_notification_count.php', function(data) {
    if (data > 0) {
       $('#unread').html(" " + data).attr("title", "You have " + data + " unread notifications on Podio").css("display", "inline-block");
     } else {
       $('#unread').html(null).css("display", "none");
     }
     setTimeout(showUnreadNotifications, 300000);
   });
}

var initNotificationChecker = function () {
  setTimeout(showUnreadNotifications, 10);
}

/*
app.nuke();

favoriteAppsValues.push({
  appId: 35997,
  appName: "Deliverables",
  appIcon: "84.png",
  spaceName: "dev",
  spaceUrl: ""
});

$.get('actions/item_new.php', { appId: 35997 }, function(data) {
  var template = data;
  var html = Mustache.to_html(template);
  $("#main").html(html);
});

app.save({key:'favoriteApps', values: favoriteAppsValues});
*/