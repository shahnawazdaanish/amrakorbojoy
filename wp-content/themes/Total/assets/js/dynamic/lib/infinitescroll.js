/*
   --------------------------------
   Infinite Scroll
   --------------------------------
   + https://github.com/paulirish/infinite-scroll
   + version 2.0b2.120519
   + Copyright 2011/12 Paul Irish & Luke Shumard
   + Licensed under the MIT license
   + Modified for Total theme...

   + Documentation: http://infinite-scroll.com/
*/
(function(e, t, n) {
    "use strict";
    t.infinitescroll = function(n, r, i) {
        this.element = t(i);
        if (!this._create(n, r)) {
            this.failed = true
        }
    };
    t.infinitescroll.defaults = {
        loading: {
            finished: n,
            finishedMsg: '',
            img: wpexInfiniteScroll.blankImg,
            msg: null,
            msgText: '',
            selector: null,
            speed: "fast",
            start: n
        },
        state: {
            isDuringAjax: false,
            isInvalidPage: false,
            isDestroyed: false,
            isDone: false,
            isPaused: false,
            isBeyondMaxPage: false,
            currPage: 1
        },
        debug: false,
        behavior: n,
        binder: t(e),
        nextSelector: "div.navigation a:first",
        navSelector: "div.navigation",
        contentSelector: null,
        extraScrollPx: 150,
        itemSelector: "div.post",
        animate: false,
        pathParse: n,
        dataType: "html",
        appendCallback: true,
        bufferPx: 40,
        errorCallback: function() {},
        infid: 0,
        pixelsFromNavToBottom: n,
        path: n,
        prefill: false,
        maxPage: n
    };
    t.infinitescroll.prototype = {
        _binding: function(t) {
            var r = this,
                i = r.options;
            i.v = "2.0b2.120520";
            if (!!i.behavior && this["_binding_" + i.behavior] !== n) {
                this["_binding_" + i.behavior].call(this);
                return
            }
            if (t !== "bind" && t !== "unbind") {
                this._debug("Binding value  " + t + " not valid");
                return false
            }
            if (t === "unbind") {
                this.options.binder.unbind("smartscroll.infscr." + r.options.infid)
            } else {
                this.options.binder[t]("smartscroll.infscr." + r.options.infid, function() {
                    r.scroll()
                })
            }
            this._debug("Binding", t)
        },
        _create: function(i, s) {
            var o = t.extend(true, {}, t.infinitescroll.defaults, i);
            this.options = o;
            var u = t(e);
            var a = this;
            if (!a._validate(i)) {
                return false
            }
            var f = t(o.nextSelector).attr("href");
            if (!f) {
                this._debug("Navigation selector not found");
                return false
            }
            o.path = o.path || this._determinepath(f);
            o.contentSelector = o.contentSelector || this.element;
            o.loading.selector = o.loading.selector || o.contentSelector;
            o.loading.msg = o.loading.msg || t('<div id="wpex-infscr-loading">' + o.loading.msgText + '</div>');
            (new Image).src = o.loading.img;
            if (o.pixelsFromNavToBottom === n) {
                o.pixelsFromNavToBottom = t(document).height() - t(o.navSelector).offset().top;
                this._debug("pixelsFromNavToBottom: " + o.pixelsFromNavToBottom)
            }
            var l = this;
            o.loading.start = o.loading.start || function() {
                t(o.navSelector).hide();
                o.loading.msg.appendTo(o.loading.selector).show();
                l.beginAjax(o);
            };
            o.loading.finished = o.loading.finished || function() {
                if (!o.state.isBeyondMaxPage) o.loading.msg.fadeOut(o.loading.speed)
            };
            o.callback = function(e, r, i) {
                if (!!o.behavior && e["_callback_" + o.behavior] !== n) {
                    e["_callback_" + o.behavior].call(t(o.contentSelector)[0], r, i)
                }
                if (s) {
                    s.call(t(o.contentSelector)[0], r, o, i)
                }
                if (o.prefill) {
                    u.bind("resize.infinite-scroll", e._prefill)
                }
            };
            if (i.debug) {
                if (Function.prototype.bind && (typeof console === "object" || typeof console === "function") && typeof console.log === "object") {
                    ["log", "info", "warn", "error", "assert", "dir", "clear", "profile", "profileEnd"].forEach(function(e) {
                        console[e] = this.call(console[e], console)
                    }, Function.prototype.bind)
                }
            }
            this._setup();
            if (o.prefill) {
                this._prefill()
            }
            return true
        },
        _prefill: function() {
            function s() {
                return r.options.contentSelector.height() <= i.height()
            }
            var r = this;
            var i = t(e);
            this._prefill = function() {
                if (s()) {
                    r.scroll()
                }
                i.bind("resize.infinite-scroll", function() {
                    if (s()) {
                        i.unbind("resize.infinite-scroll");
                        r.scroll()
                    }
                })
            };
            this._prefill()
        },
        _debug: function() {
            if (true !== this.options.debug) {
                return
            }
            if (typeof console !== "undefined" && typeof console.log === "function") {
                if (Array.prototype.slice.call(arguments).length === 1 && typeof Array.prototype.slice.call(arguments)[0] === "string") {
                    console.log(Array.prototype.slice.call(arguments).toString())
                } else {
                    console.log(Array.prototype.slice.call(arguments))
                }
            } else if (!Function.prototype.bind && typeof console !== "undefined" && typeof console.log === "object") {
                Function.prototype.call.call(console.log, console, Array.prototype.slice.call(arguments))
            }
        },
        _determinepath: function(t) {
            var r = this.options;
            if (!!r.behavior && this["_determinepath_" + r.behavior] !== n) {
                return this["_determinepath_" + r.behavior].call(this, t)
            }
            if (!!r.pathParse) {
                this._debug("pathParse manual");
                return r.pathParse(t, this.options.state.currPage + 1)
            } else if (t.match(/^(.*?)\b2\b(.*?$)/)) {
                t = t.match(/^(.*?)\b2\b(.*?$)/).slice(1)
            } else if (t.match(/^(.*?)2(.*?$)/)) {
                if (t.match(/^(.*?page=)2(\/.*|$)/)) {
                    t = t.match(/^(.*?page=)2(\/.*|$)/).slice(1);
                    return t
                }
                t = t.match(/^(.*?)2(.*?$)/).slice(1)
            } else {
                if (t.match(/^(.*?page=)1(\/.*|$)/)) {
                    t = t.match(/^(.*?page=)1(\/.*|$)/).slice(1);
                    return t
                } else {
                    this._debug("Sorry, we couldn't parse your Next (Previous Posts) URL. Verify your the css selector points to the correct A tag. If you still get this error: yell, scream, and kindly ask for help at infinite-scroll.com.");
                    r.state.isInvalidPage = true
                }
            }
            this._debug("determinePath", t);
            return t
        },
        _error: function(t) {
            var r = this.options;
            if (!!r.behavior && this["_error_" + r.behavior] !== n) {
                this["_error_" + r.behavior].call(this, t);
                return
            }
            if (t !== "destroy" && t !== "end") {
                t = "unknown"
            }
            this._debug("Error", t);
            if (t === "end" || r.state.isBeyondMaxPage) {
                this._showdonemsg()
            }
            r.state.isDone = true;
            r.state.currPage = 1;
            r.state.isPaused = false;
            r.state.isBeyondMaxPage = false;
            this._binding("unbind")
        },
        _loadcallback: function(i, s, o) {
            var u = this.options,
                a = this.options.callback,
                f = u.state.isDone ? "done" : !u.appendCallback ? "no-append" : "append",
                l;
            if (!!u.behavior && this["_loadcallback_" + u.behavior] !== n) {
                this["_loadcallback_" + u.behavior].call(this, i, s);
                return
            }
            switch (f) {
                case "done":
                    this._showdonemsg();
                    return false;
                case "no-append":
                    if (u.dataType === "html") {
                        s = "<div>" + s + "</div>";
                        s = t(s).find(u.itemSelector)
                    }
                    break;
                case "append":
                    var c = i.children();
                    if (c.length === 0) {
                        return this._error("end")
                    }
                    l = document.createDocumentFragment();
                    while (i[0].firstChild) {
                        l.appendChild(i[0].firstChild)
                    }
                    this._debug("contentSelector", t(u.contentSelector)[0]);
                    t(u.contentSelector)[0].appendChild(l);
                    s = c.get();
                    break
            }
            u.loading.finished.call(t(u.contentSelector)[0], u);
            if (u.animate) {
                var h = t(e).scrollTop() + t(u.loading.msg).height() + u.extraScrollPx + "px";
                t("html,body").animate({
                    scrollTop: h
                }, 800, function() {
                    u.state.isDuringAjax = false
                })
            }
            if (!u.animate) {
                u.state.isDuringAjax = false
            }
            a(this, s, o);
            if (u.prefill) {
                this._prefill()
            }
        },
        _nearbottom: function() {
            var i = this.options,
                s = 0 + t(document).height() - i.binder.scrollTop() - t(e).height();
            if (!!i.behavior && this["_nearbottom_" + i.behavior] !== n) {
                return this["_nearbottom_" + i.behavior].call(this)
            }
            this._debug("math:", s, i.pixelsFromNavToBottom);
            return s - i.bufferPx < i.pixelsFromNavToBottom
        },
        _pausing: function(t) {
            var r = this.options;
            if (!!r.behavior && this["_pausing_" + r.behavior] !== n) {
                this["_pausing_" + r.behavior].call(this, t);
                return
            }
            if (t !== "pause" && t !== "resume" && t !== null) {
                this._debug("Invalid argument. Toggling pause value instead")
            }
            t = t && (t === "pause" || t === "resume") ? t : "toggle";
            switch (t) {
                case "pause":
                    r.state.isPaused = true;
                    break;
                case "resume":
                    r.state.isPaused = false;
                    break;
                case "toggle":
                    r.state.isPaused = !r.state.isPaused;
                    break
            }
            this._debug("Paused", r.state.isPaused);
            return false
        },
        _setup: function() {
            var t = this.options;
            if (!!t.behavior && this["_setup_" + t.behavior] !== n) {
                this["_setup_" + t.behavior].call(this);
                return
            }
            this._binding("bind");
            return false
        },
        _showdonemsg: function() {
            var r = this.options;
            if (!!r.behavior && this["_showdonemsg_" + r.behavior] !== n) {
                this["_showdonemsg_" + r.behavior].call(this);
                return
            }
            r.loading.msg.find('.wpex-infscr-spinner').hide();
            t(this).parent().fadeOut(r.loading.speed);
            r.errorCallback.call(t(r.contentSelector)[0], "done")
        },
        _validate: function(n) {
            for (var r in n) {
                if (r.indexOf && r.indexOf("Selector") > -1 && t(n[r]).length === 0) {
                    this._debug("Your " + r + " found no elements.");
                    return false
                }
            }
            return true
        },
        bind: function() {
            this._binding("bind")
        },
        destroy: function() {
            this.options.state.isDestroyed = true;
            this.options.loading.finished();
            return this._error("destroy")
        },
        pause: function() {
            this._pausing("pause")
        },
        resume: function() {
            this._pausing("resume")
        },
        beginAjax: function(r) {
            var i = this,
                s = r.path,
                o, u, a, f;
            r.state.currPage++;
            if (r.maxPage != n && r.state.currPage > r.maxPage) {
                r.state.isBeyondMaxPage = true;
                this.destroy();
                return
            }
            o = t(r.contentSelector).is("table, tbody") ? t("<tbody/>") : t("<div/>");
            u = typeof s === "function" ? s(r.state.currPage) : s.join(r.state.currPage);
            i._debug("heading into ajax", u);
            a = r.dataType === "html" || r.dataType === "json" ? r.dataType : "html+callback";
            if (r.appendCallback && r.dataType === "html") {
                a += "+callback"
            }
            switch (a) {
                case "html+callback":
                    i._debug("Using HTML via .load() method");
                    o.load(u + " " + r.itemSelector, n, function(t) {
                        i._loadcallback(o, t, u)
                    });
                    break;
                case "html":
                    i._debug("Using " + a.toUpperCase() + " via $.ajax() method");
                    t.ajax({
                        url: u,
                        dataType: r.dataType,
                        complete: function(t, n) {
                            f = typeof t.isResolved !== "undefined" ? t.isResolved() : n === "success" || n === "notmodified";
                            if (f) {
                                i._loadcallback(o, t.responseText, u)
                            } else {
                                i._error("end")
                            }
                        }
                    });
                    break;
                case "json":
                    i._debug("Using " + a.toUpperCase() + " via $.ajax() method");
                    t.ajax({
                        dataType: "json",
                        type: "GET",
                        url: u,
                        success: function(e, t, s) {
                            f = typeof s.isResolved !== "undefined" ? s.isResolved() : t === "success" || t === "notmodified";
                            if (r.appendCallback) {
                                if (r.template !== n) {
                                    var a = r.template(e);
                                    o.append(a);
                                    if (f) {
                                        i._loadcallback(o, a)
                                    } else {
                                        i._error("end")
                                    }
                                } else {
                                    i._debug("template must be defined.");
                                    i._error("end")
                                }
                            } else {
                                if (f) {
                                    i._loadcallback(o, e, u)
                                } else {
                                    i._error("end")
                                }
                            }
                        },
                        error: function() {
                            i._debug("JSON ajax request failed.");
                            i._error("end")
                        }
                    });
                    break
            }
        },
        retrieve: function(r) {
            r = r || null;
            var i = this,
                s = i.options;
            if (!!s.behavior && this["retrieve_" + s.behavior] !== n) {
                this["retrieve_" + s.behavior].call(this, r);
                return
            }
            if (s.state.isDestroyed) {
                this._debug("Instance is destroyed");
                return false
            }
            s.state.isDuringAjax = true;
            s.loading.start.call(t(s.contentSelector)[0], s)
        },
        scroll: function() {
            var t = this.options,
                r = t.state;
            if (!!t.behavior && this["scroll_" + t.behavior] !== n) {
                this["scroll_" + t.behavior].call(this);
                return
            }
            if (r.isDuringAjax || r.isInvalidPage || r.isDone || r.isDestroyed || r.isPaused) {
                return
            }
            if (!this._nearbottom()) {
                return
            }
            this.retrieve()
        },
        toggle: function() {
            this._pausing()
        },
        unbind: function() {
            this._binding("unbind")
        },
        update: function(n) {
            if (t.isPlainObject(n)) {
                this.options = t.extend(true, this.options, n)
            }
        }
    };
    t.fn.infinitescroll = function(n, r) {
        var i = typeof n;
        switch (i) {
            case "string":
                var s = Array.prototype.slice.call(arguments, 1);
                this.each(function() {
                    var e = t.data(this, "infinitescroll");
                    if (!e) {
                        return false
                    }
                    if (!t.isFunction(e[n]) || n.charAt(0) === "_") {
                        return false
                    }
                    e[n].apply(e, s)
                });
                break;
            case "object":
                this.each(function() {
                    var e = t.data(this, "infinitescroll");
                    if (e) {
                        e.update(n)
                    } else {
                        e = new t.infinitescroll(n, r, this);
                        if (!e.failed) {
                            t.data(this, "infinitescroll", e)
                        }
                    }
                });
                break
        }
        return this
    };
    var r = t.event,
        i;
    r.special.smartscroll = {
        setup: function() {
            t(this).bind("scroll", r.special.smartscroll.handler)
        },
        teardown: function() {
            t(this).unbind("scroll", r.special.smartscroll.handler)
        },
        handler: function(e, n) {
            var r = this,
                s = arguments;
            e.type = "smartscroll";
            if (i) {
                clearTimeout(i)
            }
            i = setTimeout(function() {
                t(r).trigger("smartscroll", s)
            }, n === "execAsap" ? 0 : 100)
        }
    };
    t.fn.smartscroll = function(e) {
        return e ? this.bind("smartscroll", e) : this.trigger("smartscroll", ["execAsap"])
    }
})(window, jQuery);