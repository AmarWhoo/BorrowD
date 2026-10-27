(function ($) {
  "use strict";

  var viewFiles = {
    home: "home.html",
    books: "books.html",
    "book-details": "book-details.html",
    "my-loans": "my-loans.html",
    profile: "profile.html",
    admin: "admin.html",
    login: "login.html",
    register: "register.html"
  };
  var loadedViews = {};
  var loadingViews = {};

  function getViewName() {
    var viewName = window.location.hash.substring(1);
    return viewFiles[viewName] ? viewName : "home";
  }

  function updateActiveLink(viewName) {
    $(".navbar .nav-link").removeClass("active").attr("aria-current", null);
    $('.navbar .nav-link[href="#' + viewName + '"]')
      .addClass("active")
      .attr("aria-current", "page");
  }

  function showView(viewName) {
    $(".borrowd-view").addClass("d-none");
    $("#" + viewName + "-view").removeClass("d-none");
    updateActiveLink(viewName);
  }

  function loadView(viewName) {
    if (loadedViews[viewName]) {
      if (getViewName() === viewName) {
        showView(viewName);
      }
      return;
    }

    if (loadingViews[viewName]) {
      return;
    }

    loadingViews[viewName] = true;
    $.get("../views/" + viewFiles[viewName])
      .done(function (viewContent) {
        $("<section>", {
          id: viewName + "-view",
          class: "borrowd-view d-none"
        })
          .html(viewContent)
          .appendTo("#app");
        loadedViews[viewName] = true;
        loadingViews[viewName] = false;
        if (getViewName() === viewName) {
          showView(viewName);
        }
      })
      .fail(function () {
        loadingViews[viewName] = false;
        $("#app").html(
          '<div class="container"><p class="alert alert-danger" role="alert">Unable to load this page.</p></div>'
        );
      });
  }

  function route() {
    var viewName = getViewName();

    if (!window.location.hash) {
      window.location.hash = "home";
      return;
    }

    loadView(viewName);
  }

  $(window).on("hashchange", route);
  $(function () {
    route();
    $.each(viewFiles, function (viewName) {
      loadView(viewName);
    });
  });
})(jQuery);
