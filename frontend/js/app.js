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
  var viewSections = {};

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
    window.scrollTo({ top: 0, behavior: "auto" });
  }

  function allViewsFinished() {
    return Object.keys(viewFiles).every(function (viewName) {
      return loadedViews[viewName];
    });
  }

  function loadView(viewName) {
    if (loadedViews[viewName]) {
      if (allViewsFinished()) {
        showView(getViewName());
      }
      return;
    }

    if (loadingViews[viewName]) {
      return;
    }

    loadingViews[viewName] = true;
    viewSections[viewName] = $("<section>", {
      id: viewName + "-view",
      class: "borrowd-view d-none"
    }).appendTo("#app");

    $.get("../views/" + viewFiles[viewName])
      .done(function (viewContent) {
        viewSections[viewName].html(viewContent);
        loadedViews[viewName] = true;
        loadingViews[viewName] = false;
        if (allViewsFinished()) {
          showView(getViewName());
        }
      })
      .fail(function () {
        viewSections[viewName].html(
          '<div class="alert alert-danger" role="alert">This page could not be loaded. Please refresh the page.</div>'
        );
        console.error("Unable to load view:", viewFiles[viewName]);
        loadedViews[viewName] = true;
        loadingViews[viewName] = false;
        if (allViewsFinished() && getViewName() === viewName) {
          showView(viewName);
        }
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
