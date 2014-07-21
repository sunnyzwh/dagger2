if (typeof jQuery != "undefined") {
    var AJS = (function () {
       var D = [];
        var C = {
            version: "3.3.1",
            params: {},
            $: jQuery,
            log: function (E) {
                if (typeof console != "undefined" && console.log) {
                    console.log(E)
                }
            },
            I18n: {
                getText: function (E) {
                    return E
                }
            },
            stopEvent: function (E) {
                E.stopPropagation();
                return false
            },
            include: function (E) {
                if (!this.contains(D, E)) {
                    D.push(E);
                    var F = document.createElement("script");
                    F.src = E;
                    this.$("body").append(F)
                }
            },
            toggleClassName: function (E, F) {
                if (!(E = this.$(E))) {
                    return
                }
                E.toggleClass(F)
            },
            setVisible: function (F, E) {
                if (!(F = this.$(F))) {
                    return
                }
                var G = this.$;
                G(F).each(function () {
                    var H = G(this).hasClass("hidden");
                    if (H && E) {
                        G(this).removeClass("hidden")
                    } else {
                        if (!H && !E) {
                            G(this).addClass("hidden")
                        }
                    }
                })
            },
            setCurrent: function (E, F) {
                if (!(E = this.$(E))) {
                    return
                }
                if (F) {
                    E.addClass("current")
                } else {
                    E.removeClass("current")
                }
            },
            isVisible: function (E) {
                return !this.$(E).hasClass("hidden")
            },
            populateParameters: function () {
                var E = this;
                this.$(".parameters input").each(function () {
                    var F = this.value,
                        G = this.title || this.id;
                    if (E.$(this).hasClass("list")) {
                        if (E.params[G]) {
                            E.params[G].push(F)
                        } else {
                            E.params[G] = [F]
                        }
                    } else {
                        E.params[G] = (F.match(/^(tru|fals)e$/i) ? F.toLowerCase() == "true" : F)
                    }
                })
            },
            toInit: function (F) {
                var E = this;
                this.$(function () {
                    try {
                        F.apply(this, arguments)
                    } catch (G) {
                        E.log("Failed to run init function: " + G)
                    }
                });
                return this
            },
            indexOf: function (I, H, F) {
                var G = I.length;
                if (F == null) {
                    F = 0
                } else {
                    if (F < 0) {
                        F = Math.max(0, G + F)
                    }
                }
                for (var E = F; E < G; E++) {
                    if (I[E] === H) {
                        return E
                    }
                }
                return -1
            },
            contains: function (F, E) {
                return this.indexOf(F, E) > -1
            },
            format: function (G) {
                var E = /^((?:(?:[^']*'){2})*?[^']*?)\{(\d+)\}/,
                    F = /'(?!')/g;
                AJS.format = function (K) {
                    var I = arguments,
                        J = "",
                        H = K.match(E);
                    while (H) {
                        K = K.substring(H[0].length);
                        J += H[1].replace(F, "") + (I.length > ++H[2] ? I[H[2]] : "");
                        H = K.match(E)
                    }
                    return J += K.replace(F, "")
                };
                return AJS.format.apply(AJS, arguments)
            },
            firebug: function () {
                var E = this.$(document.createElement("script"));
                E.attr("src", "http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js");
                this.$("head").append(E);
                (function () {
                    if (window.firebug) {
                        firebug.init()
                    } else {
                        setTimeout(arguments.callee, 0)
                    }
                })()
            },
            clone: function (E) {
                return AJS.$(E).clone().removeAttr("id")
            },
            alphanum: function (L, K) {
                L = (L + "").toLowerCase();
                K = (K + "").toLowerCase();
                var G = /(\d+|\D+)/g,
                    H = L.match(G),
                    E = K.match(G),
                    J = Math.max(H.length, E.length);
                for (var F = 0; F < J; F++) {
                    if (F == H.length) {
                        return -1
                    }
                    if (F == E.length) {
                        return 1
                    }
                    var M = parseInt(H[F], 10),
                        I = parseInt(E[F], 10);
                    if (M == H[F] && I == E[F] && M != I) {
                        return (M - I) / Math.abs(M - I)
                    }
                    if ((M != H[F] || I != E[F]) && H[F] != E[F]) {
                        return H[F] < E[F] ? -1 : 1
                    }
                }
                return 0
            },
            dim: function () {
                if (AJS.dim.dim) {
                    AJS.dim.dim.remove();
                    AJS.dim.dim = null
                } else {
                    AJS.dim.dim = AJS("div").css({
                        width: "100%",
                        height: AJS.$(document).height(),
                        background: "#000",
                        opacity: 0.5,
                        position: "absolute",
                        top: 0,
                        left: 0
                    });
                    AJS.$("body").append(AJS.dim.dim)
                }
            },
            onTextResize: function (F) {
                if (typeof F == "function") {
                    if (AJS.onTextResize["on-text-resize"]) {
                        AJS.onTextResize["on-text-resize"].push(function (G) {
                            F(G)
                        })
                    } else {
                        var E = AJS("div");
                        E.css({
                            width: "1em",
                            height: "1em",
                            position: "absolute",
                            top: "-9999em",
                            left: "-9999em"
                        });
                        this.$("body").append(E);
                        E.size = E.width();
                        setInterval(function () {
                            if (E.size != E.width()) {
                                E.size = E.width();
                                for (var G = 0, H = AJS.onTextResize["on-text-resize"].length; G < H; G++) {
                                    AJS.onTextResize["on-text-resize"][G](E.size)
                                }
                            }
                        }, 0);
                        AJS.onTextResize.em = E;
                        AJS.onTextResize["on-text-resize"] = [function (G) {
                            F(G)
                        }]
                    }
                }
            },
            unbindTextResize: function (G) {
                for (var E = 0, F = AJS.onTextResize["on-text-resize"].length; E < F; E++) {
                    if (AJS.onTextResize["on-text-resize"][E] == G) {
                        return AJS.onTextResize["on-text-resize"].splice(E, 1)
                    }
                }
            },
            escape: function (E) {
                return escape(E).replace(/%u\w{4}/gi, function (F) {
                    return unescape(F)
                })
            },
            filterBySearch: function (I, N, O) {
                if (N == "") {
                    return []
                }
                var G = this.$;
                var L = (O && O.keywordsField) || "keywords";
                var K = (O && O.ignoreForCamelCase) ? "i" : "";
                var H = (O && O.matchBoundary) ? "\\b" : "";
                var F = (O && O.splitRegex) || (/\s+/);
                var J = N.split(F);
                var E = [];
                G.each(J, function () {
                    var Q = [new RegExp(H + this, "i")];
                    if (/^([A-Z][a-z]*){2,}$/.test(this)) {
                        var P = this.replace(/([A-Z][a-z]*)/g, "\\b$1[^,]*");
                        Q.push(new RegExp(P, K))
                    }
                    E.push(Q)
                });
                var M = [];
                G.each(I, function () {
                    for (var R = 0; R < E.length; R++) {
                        var P = false;
                        for (var Q = 0; Q < E[R].length; Q++) {
                            if (E[R][Q].test(this[L])) {
                                P = true;
                                break
                            }
                        }
                        if (!P) {
                            return
                        }
                    }
                    M.push(this)
                });
                return M
            },
            drawLogo: function (H) {
                options = {};
                options = AJS.$.extend(H, options);
                var L = options.scaleFactor || 1,
                    K = options.fill || "#fff",
                    J = options.stroke || "#000",
                    G = 400 * L,
                    E = 40 * L;
                strokeWidth = options.strokeWidth || 1;
                if (AJS.$(".aui-logo").size() == 0) {
                    AJS.$("body").append("<div id='aui-logo' class='aui-logo'><div>")
                }
                strokeWidth = options.strokeWidth || 1, containerID = options.containerID || ".aui-logo";
                var F = Raphael(containerID, G + 50 * L, E + 100 * L);
                var I = F.path("M 0,0 c 3.5433333,-4.7243333 7.0866667,-9.4486667 10.63,-14.173 -14.173,0 -28.346,0 -42.519,0 C -35.432667,-9.4486667 -38.976333,-4.7243333 -42.52,0 -28.346667,0 -14.173333,0 0,0 z m 277.031,28.346 c -14.17367,0 -28.34733,0 -42.521,0 C 245.14,14.173 255.77,0 266.4,-14.173 c -14.17267,0 -28.34533,0 -42.518,0 C 213.25167,0 202.62133,14.173 191.991,28.346 c -14.17333,0 -28.34667,0 -42.52,0 14.17333,-18.8976667 28.34667,-37.7953333 42.52,-56.693 -7.08667,-9.448667 -14.17333,-18.897333 -21.26,-28.346 -14.173,0 -28.346,0 -42.519,0 7.08667,9.448667 14.17333,18.897333 21.26,28.346 -14.17333,18.8976667 -28.34667,37.7953333 -42.52,56.693 -14.173333,0 -28.346667,0 -42.52,0 10.63,-14.173 21.26,-28.346 31.89,-42.519 -14.390333,0 -28.780667,0 -43.171,0 C 42.520733,1.330715e-4 31.889933,14.174867 21.26,28.347 c -42.520624,6.24e-4 -85.039187,-8.13e-4 -127.559,-0.001 11.220667,-14.961 22.441333,-29.922 33.662,-44.883 -6.496,-8.661 -12.992,-17.322 -19.488,-25.983 5.905333,0 11.810667,0 17.716,0 -10.63,-14.173333 -21.26,-28.346667 -31.89,-42.52 14.173333,0 28.346667,0 42.52,0 10.63,14.173333 21.26,28.346667 31.89,42.52 14.173333,0 28.3466667,0 42.52,0 -10.63,-14.173333 -21.26,-28.346667 -31.89,-42.52 14.1733333,0 28.3466667,0 42.52,0 10.63,14.173333 21.26,28.346667 31.89,42.52 14.390333,0 28.780667,0 43.171,0 -10.63,-14.173333 -21.26,-28.346667 -31.89,-42.52 42.51967,0 85.03933,0 127.559,0 10.63033,14.173333 21.26067,28.346667 31.891,42.52 14.17267,0 28.34533,0 42.518,0 -10.63,-14.173333 -21.26,-28.346667 -31.89,-42.52 14.17367,0 28.34733,0 42.521,0 14.17333,18.897667 28.34667,37.795333 42.52,56.693 -14.17333,18.8976667 -28.34667,37.7953333 -42.52,56.693 z");
                console.log(E);
                I.scale(L, -L, 0, 0);
                I.translate(120 * L, E);
                I.attr("fill", K);
                I.attr("stroke", J);
                I.attr("stroke-width", strokeWidth)
            }
        };
        if (typeof AJS != "undefined") {
            for (var B in AJS) {
                C[B] = AJS[B]
            }
        }
        var A = function () {
                var E = null;
                if (arguments.length && typeof arguments[0] == "string") {
                    E = arguments.callee.$(document.createElement(arguments[0]));
                    if (arguments.length == 2) {
                        E.html(arguments[1])
                    }
                }
                return E
            };
        for (var B in C) {
            A[B] = C[B]
        }
        return A
    })();
    AJS.$(function () {
        AJS.populateParameters()
    })
}
if (typeof console == "undefined") {
    console = {
        messages: [],
        log: function (A) {
            this.messages.push(A)
        },
        show: function () {
            alert(this.messages.join("\n"));
            this.messages = []
        }
    }
} else {
    console.show = function () {}
}
AJS.$.ajaxSettings.traditional = true;
AJS.bind = function (A, C, B) {
    try {
        return jQuery(window).bind(A, C, B)
    } catch (D) {
        AJS.log("error while binding: " + D.message)
    }
};
AJS.unbind = function (A, B) {
    try {
        return jQuery(window).unbind(A, B)
    } catch (C) {
        AJS.log("error while unbinding: " + C.message)
    }
};
AJS.trigger = function (A, C) {
    try {
        return jQuery(window).trigger(A, C)
    } catch (B) {
        AJS.log("error while triggering: " + B.message)
    }
};
(function () {
    var E = "AJS.conglomerate.cookie";

    function B(F, I) {
        I = I || "";
        var H = new RegExp(F + "=([^|]+)"),
            G = I.match(H);
        return G && G[1]
    }
    function A(F, H, J) {
        var G = new RegExp("\\s*" + F + "=[^|]+(\\||$)");
        J = J || "";
        J = J.replace(G, "") + (J ? "|" : "");
        if (H) {
            var I = F + "=" + H;
            if (J.length + I.length < 4020) {
                J += I
            }
        }
        return J
    }
    function D(F) {
        var H = new RegExp(F + "=([^;]+)"),
            G = document.cookie.match(H);
        return G && G[1]
    }
    function C(F, H, J) {
        var G = "";
        if (J) {
            var I = new Date();
            I.setTime(+I + J * 24 * 60 * 60 * 1000);
            G = "; expires=" + I.toGMTString()
        }
        document.cookie = F + "=" + H + G + ";path=/"
    }
    AJS.Cookie = {
        save: function (G, H, F) {
            var I = D(E);
            I = A(G, H, I);
            C(E, I, F || 365)
        },
        read: function (G, F) {
            var I = D(E);
            var H = B(G, I);
            if (H != null) {
                return H
            }
            return F
        },
        erase: function (F) {
            this.save(F, "")
        }
    }
})();
AJS.dim = function (A) {
    if (!AJS.dim.dim) {
        AJS.dim.dim = AJS("div").addClass("aui-blanket");
        if (AJS.$.browser.msie) {
            AJS.dim.dim.css({
                width: "200%",
                height: Math.max(AJS.$(document).height(), AJS.$(window).height()) + "px"
            })
        }
        AJS.$("body").append(AJS.dim.dim);
        if (AJS.$.browser.msie && typeof AJS.hasFlash === "undefined" && A === false) {
            AJS.hasFlash = false;
            AJS.$("object, embed, iframe").each(function () {
                if (this.nodeName.toLowerCase() === "iframe") {
                    if (AJS.$(this).contents().find("object, embed").length) {
                        AJS.hasFlash = true;
                        return false
                    }
                } else {
                    AJS.hasFlash = true;
                    return false
                }
            })
        }
        if (AJS.$.browser.msie && (A !== false || AJS.hasFlash)) {
            AJS.dim.shim = AJS.$('<iframe frameBorder="0" class="aui-blanket-shim" src="javascript:false;"/>');
            AJS.dim.shim.css({
                height: Math.max(AJS.$(document).height(), AJS.$(window).height()) + "px"
            });
            AJS.$("body").append(AJS.dim.shim)
        }
        if (AJS.$.browser.msie && parseInt(AJS.$.browser.version, 10) < 8) {
            AJS.$("html").css("overflow", "hidden")
        } else {
            AJS.$("body").css("overflow", "hidden")
        }
    }
};
AJS.undim = function () {
    if (AJS.dim.dim) {
        AJS.dim.dim.remove();
        AJS.dim.dim = null;
        if (AJS.dim.shim) {
            AJS.dim.shim.remove()
        }
        if (AJS.$.browser.msie && parseInt(AJS.$.browser.version, 10) < 8) {
            AJS.$("html").css("overflow", "")
        } else {
            AJS.$("body").css("overflow", "")
        }
        if (AJS.$.browser.safari) {
            var A = AJS.$(window).scrollTop();
            AJS.$(window).scrollTop(10 + 5 * (A == 10)).scrollTop(A)
        }
    }
};
AJS.popup = function (D) {
    var G = {
        width: 800,
        height: 600,
        closeOnOutsideClick: false,
        keypressListener: function (H) {
            if (H.keyCode === 27 && B.is(":visible")) {
                E.hide()
            }
        }
    };
    if (typeof D != "object") {
        D = {
            width: arguments[0],
            height: arguments[1],
            id: arguments[2]
        };
        D = AJS.$.extend({}, D, arguments[3])
    }
    D = AJS.$.extend({}, G, D);
    var B = AJS("div").addClass("aui-popup");
    if (D.id) {
        B.attr("id", D.id)
    }
    var A = 3000;
    AJS.$(".aui-dialog").each(function () {
        var H = AJS.$(this);
        A = (H.css("z-index") > A) ? H.css("z-index") : A
    });
    var F = (function (I, H) {
        D.width = (I = (I || D.width));
        D.height = (H = (H || D.height));
        B.css({
            marginTop: -Math.round(H / 2) + "px",
            marginLeft: -Math.round(I / 2) + "px",
            width: I,
            height: H,
            background: "#fff",
            "z-index": parseInt(A, 10) + 2
        });
        return arguments.callee
    })(D.width, D.height);
    AJS.$("body").append(B);
    B.hide();
    B.enable();
    var C = AJS.$(".aui-blanket");
    var E = {
        changeSize: function (H, I) {
            if ((H && H != D.width) || (I && I != D.height)) {
                F(H, I)
            }
            this.show()
        },
        show: function () {
            var H = function () {
                    var I = 5;
                    if (AJS.$.browser.msie && ~~ (AJS.$.browser.version) < 9) {
                        I = 3
                    }
                    AJS.$(document).keydown(D.keypressListener);
                    AJS.dim();
                    C = AJS.$(".aui-blanket");
                    if (C.size() != 0 && D.closeOnOutsideClick) {
                        C.click(function () {
                            if (B.is(":visible")) {
                                E.hide()
                            }
                        })
                    }
                    B.show();
                    if (!this.shadow) {
                        var J = B.offset();
                        this.shadow = Raphael.shadow(J.top, J.left, D.width, D.height, {
                            target: B[0],
                            zindex: (B.css("z-index") - 1)
                        });
                        this.shadow.css({
                            position: "fixed",
                            top: "50%",
                            left: "50%",
                            marginLeft: -(D.width / 2 - I) + "px",
                            marginTop: -(D.height / 2 - I) + "px"
                        })
                    }
                    AJS.popup.current = this;
                    AJS.$(document).trigger("showLayer", ["popup", this])
                };
            H.call(this);
            this.show = H
        },
        hide: function () {
            AJS.$(document).unbind("keydown", D.keypressListener);
            C.unbind();
            this.element.hide();
            if (this.shadow) {
                this.shadow.remove();
                this.shadow = null
            }
            if (AJS.$(".aui-dialog:visible").size() == 0) {
                AJS.undim()
            }
            AJS.$(document).trigger("hideLayer", ["popup", this]);
            AJS.popup.current = null;
            this.enable()
        },
        element: B,
        remove: function () {
            if (this.shadow) {
                this.shadow.remove();
                this.shadow = null;
                this.shadowParent.remove();
                this.shadowParent = null
            }
            B.remove();
            this.element = null
        },
        disable: function () {
            if (!this.disabled) {
                this.popupBlanket = AJS.$("<div class='dialog-blanket'> </div>").css({
                    height: B.height(),
                    width: B.width()
                });
                B.append(this.popupBlanket);
                this.disabled = true
            }
        },
        enable: function () {
            if (this.disabled) {
                this.disabled = false;
                this.popupBlanket.remove();
                this.popupBlanket = null
            }
        }
    };
    return E
};
(function () {
    function A(J, H, G, I) {
        if (!J.buttonpanel) {
            J.addButtonPanel()
        }
        this.page = J;
        this.onclick = G;
        this._onclick = function () {
            G.call(this, J.dialog, J)
        };
        this.item = AJS("button", H).addClass("button-panel-button");
        if (I) {
            this.item.addClass(I)
        }
        if (typeof G == "function") {
            this.item.click(this._onclick)
        }
        J.buttonpanel.append(this.item);
        this.id = J.button.length;
        J.button[this.id] = this
    }
    function B(K, I, H, J, G) {
        if (!K.buttonpanel) {
            K.addButtonPanel()
        }
        if (!G) {
            G = "#"
        }
        this.page = K;
        this.onclick = H;
        this._onclick = function () {
            H.call(this, K.dialog, K)
        };
        this.item = AJS("a", I).attr("href", G).addClass("button-panel-link");
        if (J) {
            this.item.addClass(J)
        }
        if (typeof H == "function") {
            this.item.click(this._onclick)
        }
        K.buttonpanel.append(this.item);
        this.id = K.button.length;
        K.button[this.id] = this
    }
    function D(I, H) {
        var G = I == "left" ? -1 : 1;
        return function (M) {
            var K = this.page[H];
            if (this.id != ((G == 1) ? K.length - 1 : 0)) {
                G *= (M || 1);
                K[this.id + G].item[(G < 0 ? "before" : "after")](this.item);
                K.splice(this.id, 1);
                K.splice(this.id + G, 0, this);
                for (var J = 0, L = K.length; J < L; J++) {
                    if (H == "panel" && this.page.curtab == K[J].id) {
                        this.page.curtab = J
                    }
                    K[J].id = J
                }
            }
            return this
        }
    }
    function F(G) {
        return function () {
            this.page[G].splice(this.id, 1);
            for (var H = 0, I = this.page[G].length; H < I; H++) {
                this.page[G][H].id = H
            }
            this.item.remove()
        }
    }
    A.prototype.moveUp = A.prototype.moveLeft = D("left", "button");
    A.prototype.moveDown = A.prototype.moveRight = D("right", "button");
    A.prototype.remove = F("button");
    A.prototype.html = function (G) {
        return this.item.html(G)
    };
    A.prototype.onclick = function (G) {
        if (typeof G == "undefined") {
            return this.onclick
        } else {
            this.item.unbind("click", this._onclick);
            this._onclick = function () {
                G.call(this, page.dialog, page)
            };
            if (typeof G == "function") {
                this.item.click(this._onclick)
            }
        }
    };
    var E = function (M, N, G, L, J) {
            if (!(G instanceof AJS.$)) {
                G = AJS.$(G)
            }
            this.dialog = M.dialog;
            this.page = M;
            this.id = M.panel.length;
            this.button = AJS("button").html(N).addClass("item-button");
            if (J) {
                this.button[0].id = J
            }
            this.item = AJS("li").append(this.button).addClass("page-menu-item");
            this.body = AJS("div").append(G).addClass("dialog-panel-body").css("height", M.dialog.height + "px");
            this.padding = 10;
            if (L) {
                this.body.addClass(L)
            }
            var I = M.panel.length,
                K = this;
            M.menu.append(this.item);
            M.body.append(this.body);
            M.panel[I] = this;
            var H = function () {
                    var O;
                    if (M.curtab + 1) {
                        O = M.panel[M.curtab];
                        O.body.hide();
                        O.item.removeClass("selected");
                        (typeof O.onblur == "function") && O.onblur()
                    }
                    M.curtab = K.id;
                    K.body.show();
                    K.item.addClass("selected");
                    (typeof K.onselect == "function") && K.onselect();
                    (typeof M.ontabchange == "function") && M.ontabchange(K, O)
                };
            if (!this.button.click) {
                AJS.log("atlassian-dialog:Panel:constructor - this.button.click false");
                this.button.onclick = H
            } else {
                this.button.click(H)
            }
            H();
            if (I == 0) {
                M.menu.css("display", "none")
            } else {
                M.menu.show()
            }
        };
    E.prototype.select = function () {
        this.button.click()
    };
    E.prototype.moveUp = E.prototype.moveLeft = D("left", "panel");
    E.prototype.moveDown = E.prototype.moveRight = D("right", "panel");
    E.prototype.remove = F("panel");
    E.prototype.html = function (G) {
        if (G) {
            this.body.html(G);
            return this
        } else {
            return this.body.html()
        }
    };
    E.prototype.setPadding = function (G) {
        if (!isNaN(+G)) {
            this.body.css("padding", +G);
            this.padding = +G;
            this.page.recalcSize()
        }
        return this
    };
    var C = function (G, H) {
            this.dialog = G;
            this.id = G.page.length;
            this.element = AJS("div").addClass("dialog-components");
            this.body = AJS("div").addClass("dialog-page-body");
            this.menu = AJS("ul").addClass("dialog-page-menu").css("height", G.height + "px");
            this.body.append(this.menu);
            this.curtab;
            this.panel = [];
            this.button = [];
            if (H) {
                this.body.addClass(H)
            }
            G.popup.element.append(this.element.append(this.menu).append(this.body));
            G.page[G.page.length] = this
        };
    C.prototype.recalcSize = function () {
        var G = this.header ? 43 : 0;
        var J = this.buttonpanel ? 43 : 0;
        for (var I = this.panel.length; I--;) {
            var H = this.dialog.height - G - J;
            this.panel[I].body.css("height", H - this.panel[I].padding * 2);
            this.menu.css("height", H - parseFloat(this.menu.css("padding-top")))
        }
    };
    C.prototype.addButtonPanel = function () {
        this.buttonpanel = AJS("div").addClass("dialog-button-panel");
        this.element.append(this.buttonpanel)
    };
    C.prototype.addPanel = function (J, G, I, H) {
        new E(this, J, G, I, H);
        this.recalcSize();
        return this
    };
    C.prototype.addHeader = function (H, G) {
        if (this.header) {
            this.header.remove()
        }
        this.header = AJS("h2").html(H || "").addClass("dialog-title");
        G && this.header.addClass(G);
        this.element.prepend(this.header);
        this.recalcSize();
        return this
    };
    C.prototype.addButton = function (H, G, I) {
        new A(this, H, G, I);
        this.recalcSize();
        return this
    };
    C.prototype.addLink = function (I, H, J, G) {
        new B(this, I, H, J, G);
        this.recalcSize();
        return this
    };
    C.prototype.gotoPanel = function (G) {
        this.panel[G.id || G].select()
    };
    C.prototype.getCurrentPanel = function () {
        return this.panel[this.curtab]
    };
    C.prototype.hide = function () {
        this.element.hide()
    };
    C.prototype.show = function () {
        this.element.show()
    };
    C.prototype.remove = function () {
        this.element.remove()
    };
    AJS.Dialog = function (I, G, J) {
        var H = {};
        if (!+I) {
            H = Object(I);
            I = H.width;
            G = H.height;
            J = H.id
        }
        this.height = G || 480;
        this.width = I || 640;
        this.id = J;
        H = AJS.$.extend({}, H, {
            width: this.width,
            height: this.height,
            id: this.id
        });
        this.popup = AJS.popup(H);
        this.popup.element.addClass("aui-dialog");
        this.page = [];
        this.curpage = 0;
        new C(this)
    };
    AJS.Dialog.prototype.addHeader = function (H, G) {
        this.page[this.curpage].addHeader(H, G);
        return this
    };
    AJS.Dialog.prototype.addButton = function (H, G, I) {
        this.page[this.curpage].addButton(H, G, I);
        return this
    };
    AJS.Dialog.prototype.addLink = function (I, H, J, G) {
        this.page[this.curpage].addLink(I, H, J, G);
        return this
    };
    AJS.Dialog.prototype.addSubmit = function (H, G) {
        this.page[this.curpage].addButton(H, G, "button-panel-submit-button");
        return this
    };
    AJS.Dialog.prototype.addCancel = function (H, G) {
        this.page[this.curpage].addLink(H, G, "button-panel-cancel-link");
        return this
    };
    AJS.Dialog.prototype.addButtonPanel = function () {
        this.page[this.curpage].addButtonPanel();
        return this
    };
    AJS.Dialog.prototype.addPanel = function (J, G, I, H) {
        this.page[this.curpage].addPanel(J, G, I, H);
        return this
    };
    AJS.Dialog.prototype.addPage = function (G) {
        new C(this, G);
        this.page[this.curpage].hide();
        this.curpage = this.page.length - 1;
        return this
    };
    AJS.Dialog.prototype.nextPage = function () {
        this.page[this.curpage++].hide();
        if (this.curpage >= this.page.length) {
            this.curpage = 0
        }
        this.page[this.curpage].show();
        return this
    };
    AJS.Dialog.prototype.prevPage = function () {
        this.page[this.curpage--].hide();
        if (this.curpage < 0) {
            this.curpage = this.page.length - 1
        }
        this.page[this.curpage].show();
        return this
    };
    AJS.Dialog.prototype.gotoPage = function (G) {
        this.page[this.curpage].hide();
        this.curpage = G;
        if (this.curpage < 0) {
            this.curpage = this.page.length - 1
        } else {
            if (this.curpage >= this.page.length) {
                this.curpage = 0
            }
        }
        this.page[this.curpage].show();
        return this
    };
    AJS.Dialog.prototype.getPanel = function (H, I) {
        var G = (I == null) ? this.curpage : H;
        if (I == null) {
            I = H
        }
        return this.page[G].panel[I]
    };
    AJS.Dialog.prototype.getPage = function (G) {
        return this.page[G]
    };
    AJS.Dialog.prototype.getCurrentPanel = function () {
        return this.page[this.curpage].getCurrentPanel()
    };
    AJS.Dialog.prototype.gotoPanel = function (I, H) {
        if (H != null) {
            var G = I.id || I;
            this.gotoPage(G)
        }
        this.page[this.curpage].gotoPanel(typeof H == "undefined" ? I : H)
    };
    AJS.Dialog.prototype.show = function () {
        this.popup.show();
        return this
    };
    AJS.Dialog.prototype.hide = function () {
        this.popup.hide();
        return this
    };
    AJS.Dialog.prototype.remove = function () {
        this.popup.hide();
        this.popup.remove()
    };
    AJS.Dialog.prototype.disable = function () {
        this.popup.disable();
        return this
    };
    AJS.Dialog.prototype.enable = function () {
        this.popup.enable();
        return this
    };
    AJS.Dialog.prototype.get = function (O) {
        var H = [],
            N = this;
        var P = '#([^"][^ ]*|"[^"]*")';
        var Q = ":(\\d+)";
        var I = "page|panel|button|header";
        var J = "(?:(" + I + ")(?:" + P + "|" + Q + ")?|" + P + ")";
        var L = new RegExp("(?:^|,)\\s*" + J + "(?:\\s+" + J + ")?\\s*(?=,|$)", "ig");
        (O + "").replace(L, function (b, R, a, S, Y, X, U, c, Z) {
            R = R && R.toLowerCase();
            var T = [];
            if (R == "page" && N.page[S]) {
                T.push(N.page[S]);
                R = X;
                R = R && R.toLowerCase();
                a = U;
                S = c;
                Y = Z
            } else {
                T = N.page
            }
            a = a && (a + "").replace(/"/g, "");
            U = U && (U + "").replace(/"/g, "");
            Y = Y && (Y + "").replace(/"/g, "");
            Z = Z && (Z + "").replace(/"/g, "");
            if (R || Y) {
                for (var W = T.length; W--;) {
                    if (Y || (R == "panel" && (a || (!a && S == null)))) {
                        for (var V = T[W].panel.length; V--;) {
                            if (T[W].panel[V].button.html() == Y || T[W].panel[V].button.html() == a || (R == "panel" && !a && S == null)) {
                                H.push(T[W].panel[V])
                            }
                        }
                    }
                    if (Y || (R == "button" && (a || (!a && S == null)))) {
                        for (var V = T[W].button.length; V--;) {
                            if (T[W].button[V].item.html() == Y || T[W].button[V].item.html() == a || (R == "button" && !a && S == null)) {
                                H.push(T[W].button[V])
                            }
                        }
                    }
                    if (T[W][R] && T[W][R][S]) {
                        H.push(T[W][R][S])
                    }
                    if (R == "header" && T[W].header) {
                        H.push(T[W].header)
                    }
                }
            } else {
                H = H.concat(T)
            }
        });
        var M = {
            length: H.length
        };
        for (var K = H.length; K--;) {
            M[K] = H[K];
            for (var G in H[K]) {
                if (!(G in M)) {
                    (function (R) {
                        M[R] = function () {
                            for (var S = this.length; S--;) {
                                if (typeof this[S][R] == "function") {
                                    this[S][R].apply(this[S], arguments)
                                }
                            }
                        }
                    })(G)
                }
            }
        }
        return M
    };
    AJS.Dialog.prototype.updateHeight = function () {
        var G = 0;
        for (var H = 0; this.getPanel(H); H++) {
            if (this.getPanel(H).body.css({
                height: "auto",
                display: "block"
            }).outerHeight() > G) {
                G = this.getPanel(H).body.outerHeight()
            }
            if (H !== this.page[this.curpage].curtab) {
                this.getPanel(H).body.css({
                    display: "none"
                })
            }
        }
        for (H = 0; this.getPanel(H); H++) {
            this.getPanel(H).body.css({
                height: G || this.height
            })
        }
        this.page[0].menu.height(G);
        this.height = G + 87;
        this.popup.changeSize(undefined, G + 87)
    };
    AJS.Dialog.prototype.getCurPanel = function () {
        return this.getPanel(this.page[this.curpage].curtab)
    };
    AJS.Dialog.prototype.getCurPanelButton = function () {
        return this.getCurPanel().button
    }
})();
AJS.dropDown = function (L, E) {
    var U = null,
        I = [],
        Q = false,
        H = AJS.$(document),
        C = {
            item: "li:has(a)",
            activeClass: "active",
            alignment: "right",
            displayHandler: function (W) {
                return W.name
            },
            escapeHandler: function () {
                this.hide("escape");
                return false
            },
            hideHandler: function () {},
            moveHandler: function (X, W) {}
        };
    AJS.$.extend(C, E);
    C.alignment = {
        left: "left",
        right: "right"
    }[C.alignment.toLowerCase()] || "left";
    if (L && L.jquery) {
        U = L
    } else {
        if (typeof L == "string") {
            U = AJS.$(L)
        } else {
            if (L && L.constructor == Array) {
                U = AJS("div").addClass("aui-dropdown").toggleClass("hidden", !! C.isHiddenByDefault);
                for (var P = 0, K = L.length; P < K; P++) {
                    var J = AJS("ol");
                    for (var O = 0, S = L[P].length; O < S; O++) {
                        var M = AJS("li");
                        var G = L[P][O];
                        if (G.href) {
                            M.append(AJS("a").html("<span>" + C.displayHandler(G) + "</span>").attr({
                                href: G.href
                            }).addClass(G.className));
                            AJS.$.data(AJS.$("a > span", M)[0], "properties", G)
                        } else {
                            M.html(G.html).addClass(G.className)
                        }
                        if (G.icon) {
                            M.prepend(AJS("img").attr("src", G.icon))
                        }
                        if (G.insideSpanIcon) {
                            M.children("a").prepend(AJS("span").attr("class", "icon"))
                        }
                        AJS.$.data(M[0], "properties", G);
                        J.append(M)
                    }
                    if (P == K - 1) {
                        J.addClass("last")
                    }
                    U.append(J)
                }
                AJS.$("body").append(U)
            } else {
                throw new Error("AJS.dropDown function was called with illegal parameter. Should be AJS.$ object, AJS.$ selector or array.")
            }
        }
    }
    var F = function () {
            N(+1)
        };
    var T = function () {
            N(-1)
        };
    var N = function (Z) {
            var Y = !Q,
                W = AJS.dropDown.current.$[0],
                X = AJS.dropDown.current.links,
                a = W.focused;
            Q = true;
            W.focused = (typeof W.focused == "number" ? W.focused : -1);
            if (!AJS.dropDown.current) {
                AJS.log("move - not current, aborting");
                return true
            }
            W.focused = W.focused + Z;
            if (W.focused < 0) {
                W.focused = X.length - 1
            }
            if (W.focused > X.length - 1) {
                W.focused = 0
            }
            C.moveHandler(AJS.$(X[W.focused]), Z < 0 ? "up" : "down");
            if (Y && X.length) {
                AJS.$(X[W.focused]).addClass(C.activeClass);
                Q = false
            } else {
                if (!X.length) {
                    Q = false
                }
            }
        };
    var V = function (Y) {
            if (!AJS.dropDown.current) {
                return true
            }
            var Z = Y.which,
                W = AJS.dropDown.current.$[0],
                X = AJS.dropDown.current.links;
            AJS.dropDown.current.cleanActive();
            switch (Z) {
            case 40:
                F();
                break;
            case 38:
                T();
                break;
            case 27:
                return C.escapeHandler.call(AJS.dropDown.current, Y);
            case 13:
                if (W.focused >= 0) {
                    if (!C.selectionHandler) {
                        if (AJS.$(X[W.focused]).attr("nodeName") != "a") {
                            return AJS.$("a", X[W.focused]).trigger("focus")
                        } else {
                            return AJS.$(X[W.focused]).trigger("focus")
                        }
                    } else {
                        return C.selectionHandler.call(AJS.dropDown.current, Y, AJS.$(X[W.focused]))
                    }
                }
                return true;
            default:
                if (X.length) {
                    AJS.$(X[W.focused]).addClass(C.activeClass)
                }
                return true
            }
            Y.stopPropagation();
            Y.preventDefault();
            return false
        };
    var A = function (W) {
            if (!((W && W.which && (W.which == 3)) || (W && W.button && (W.button == 2)) || false)) {
                if (AJS.dropDown.current) {
                    AJS.dropDown.current.hide("click")
                }
            }
        };
    var D = function (W) {
            return function () {
                if (!AJS.dropDown.current) {
                    return
                }
                AJS.dropDown.current.cleanFocus();
                this.originalClass = this.className;
                AJS.$(this).addClass(C.activeClass);
                AJS.dropDown.current.$[0].focused = W
            }
        };
    var R = function (W) {
            if (W.button || W.metaKey || W.ctrlKey || W.shiftKey) {
                return true
            }
            if (AJS.dropDown.current && C.selectionHandler) {
                C.selectionHandler.call(AJS.dropDown.current, W, AJS.$(this))
            }
        };
    var B = function (X) {
            var W = false;
            if (X.data("events")) {
                AJS.$.each(X.data("events"), function (Y, Z) {
                    AJS.$.each(Z, function (b, a) {
                        if (R === a) {
                            W = true;
                            return false
                        }
                    })
                })
            }
            return W
        };
    U.each(function () {
        var W = this,
            Y = AJS.$(this),
            Z;
        var X = {
            reset: function () {
                Z = AJS.$.extend(Z || {}, {
                    $: Y,
                    links: AJS.$(C.item || "li:has(a)", W),
                    cleanActive: function () {
                        if (W.focused + 1 && Z.links.length) {
                            AJS.$(Z.links[W.focused]).removeClass(C.activeClass)
                        }
                    },
                    cleanFocus: function () {
                        Z.cleanActive();
                        W.focused = -1
                    },
                    moveDown: F,
                    moveUp: T,
                    moveFocus: V,
                    getFocusIndex: function () {
                        return (typeof W.focused == "number") ? W.focused : -1
                    }
                });
                Z.links.each(function (a) {
                    var b = AJS.$(this);
                    if (!B(b)) {
                        b.hover(D(a), Z.cleanFocus);
                        b.click(R)
                    }
                });
                return arguments.callee
            }(),
            appear: function (a) {
                if (a) {
                    Y.removeClass("hidden");
                    Y.addClass("aui-dropdown-" + C.alignment)
                } else {
                    Y.addClass("hidden")
                }
            },
            fade: function (a) {
                if (a) {
                    Y.fadeIn("fast")
                } else {
                    Y.fadeOut("fast")
                }
            },
            scroll: function (a) {
                if (a) {
                    Y.slideDown("fast")
                } else {
                    Y.slideUp("fast")
                }
            }
        };
        Z.addCallback = function (b, a) {
            return AJS.$.aop.after({
                target: this,
                method: b
            }, a)
        };
        Z.reset = X.reset();
        Z.show = function (a) {
            this.alignment = C.alignment;
            A();
            AJS.dropDown.current = this;
            this.method = a || this.method || "appear";
            this.timer = setTimeout(function () {
                H.click(A)
            }, 0);
            H.keydown(V);
            if (C.firstSelected && this.links[0]) {
                D(0).call(this.links[0])
            }
            AJS.$(W.offsetParent).css({
                zIndex: 2000
            });
            X[this.method](true);
            AJS.$(document).trigger("showLayer", ["dropdown", AJS.dropDown.current])
        };
        Z.hide = function (a) {
            this.method = this.method || "appear";
            AJS.$(Y.get(0).offsetParent).css({
                zIndex: ""
            });
            this.cleanFocus();
            X[this.method](false);
            H.unbind("click", A).unbind("keydown", V);
            AJS.$(document).trigger("hideLayer", ["dropdown", AJS.dropDown.current]);
            AJS.dropDown.current = null;
            return a
        };
        Z.addCallback("reset", function () {
            if (C.firstSelected && this.links[0]) {
                D(0).call(this.links[0])
            }
        });
        if (!AJS.dropDown.iframes) {
            AJS.dropDown.iframes = []
        }
        AJS.dropDown.createShims = function () {
            AJS.$("iframe").each(function (a) {
                var b = this;
                if (!b.shim) {
                    b.shim = AJS.$("<div />").addClass("shim hidden").appendTo("body");
                    AJS.dropDown.iframes.push(b)
                }
            });
            return arguments.callee
        }();
        Z.addCallback("show", function () {
            AJS.$(AJS.dropDown.iframes).each(function () {
                var a = AJS.$(this);
                if (a.is(":visible")) {
                    var b = a.offset();
                    b.height = a.height();
                    b.width = a.width();
                    this.shim.css({
                        left: b.left + "px",
                        top: b.top + "px",
                        height: b.height + "px",
                        width: b.width + "px"
                    }).removeClass("hidden")
                }
            })
        });
        Z.addCallback("hide", function () {
            AJS.$(AJS.dropDown.iframes).each(function () {
                this.shim.addClass("hidden")
            });
            C.hideHandler()
        });
        (function () {
            var a = function () {
                    var b = this.$.offset();
                    if (this.shadow) {
                        this.shadow.remove()
                    }
                    if (this.$.is(":visible")) {
                        this.shadow = Raphael.shadow(0, 0, this.$.outerWidth(true), this.$.outerHeight(true), {
                            target: this.$[0]
                        });
                        this.shadow.css("top", this.$.css("top"));
                        if (this.alignment == "right") {
                            this.shadow.css("left", "")
                        } else {
                            this.shadow.css("left", "0px")
                        }
                    }
                };
            Z.addCallback("reset", a);
            Z.addCallback("show", a);
            Z.addCallback("hide", function () {
                if (this.shadow) {
                    this.shadow.remove()
                }
            })
        })();
        if (AJS.$.browser.msie) {
            (function () {
                var a = function () {
                        if (this.$.is(":visible")) {
                            if (!this.iframeShim) {
                                this.iframeShim = AJS.$('<iframe class="dropdown-shim" src="javascript:false;" frameBorder="0" />').insertBefore(this.$)
                            }
                            this.iframeShim.css({
                                display: "block",
                                top: this.$.css("top"),
                                width: this.$.outerWidth() + "px",
                                height: this.$.outerHeight() + "px"
                            });
                            if (C.alignment == "left") {
                                this.iframeShim.css({
                                    left: "0px"
                                })
                            } else {
                                this.iframeShim.css({
                                    right: "0px"
                                })
                            }
                        }
                    };
                Z.addCallback("reset", a);
                Z.addCallback("show", a);
                Z.addCallback("hide", function () {
                    if (this.iframeShim) {
                        this.iframeShim.css({
                            display: "none"
                        })
                    }
                })
            })()
        }
        I.push(Z)
    });
    return I
};
AJS.dropDown.getAdditionalPropertyValue = function (D, A) {
    var C = D[0];
    if (!C || (typeof C.tagName != "string") || C.tagName.toLowerCase() != "li") {
        AJS.log("AJS.dropDown.getAdditionalPropertyValue : item passed in should be an LI element wrapped by jQuery")
    }
    var B = AJS.$.data(C, "properties");
    return B ? B[A] : null
};
AJS.dropDown.removeAllAdditionalProperties = function (A) {};
AJS.dropDown.Standard = function (H) {
    var C = [],
        G, B = {
            selector: ".aui-dd-parent",
            dropDown: ".aui-dropdown",
            trigger: ".aui-dd-trigger"
        };
    AJS.$.extend(B, H);
    var F = function (I, L, K, J) {
            AJS.$.extend(J, {
                trigger: I
            });
            L.addClass("dd-allocated");
            K.addClass("hidden");
            if (B.isHiddenByDefault == false) {
                J.show()
            }
            J.addCallback("show", function () {
                L.addClass("active")
            });
            J.addCallback("hide", function () {
                L.removeClass("active")
            })
        };
    var A = function (K, I, L, J) {
            if (J != AJS.dropDown.current) {
                L.css({
                    top: I.outerHeight()
                });
                J.show();
                K.stopImmediatePropagation()
            }
            K.preventDefault()
        };
    if (B.useLiveEvents) {
        var D = [];
        var E = [];
        AJS.$(B.trigger).live("click", function (L) {
            var I = AJS.$(this);
            var N, M, J;
            var K;
            if ((K = AJS.$.inArray(this, D)) >= 0) {
                var O = E[K];
                N = O.parent;
                M = O.dropdown;
                J = O.ddcontrol
            } else {
                N = I.closest(B.selector);
                M = N.find(B.dropDown);
                if (M.length === 0) {
                    return
                }
                J = AJS.dropDown(M, B)[0];
                if (!J) {
                    return
                }
                D.push(this);
                O = {
                    parent: N,
                    dropdown: M,
                    ddcontrol: J
                };
                F(I, N, M, J);
                E.push(O)
            }
            A(L, I, M, J)
        })
    } else {
        if (this instanceof AJS.$) {
            G = this
        } else {
            G = AJS.$(B.selector)
        }
        G = G.not(".dd-allocated").filter(":has(" + B.dropDown + ")").filter(":has(" + B.trigger + ")");
        G.each(function () {
            var L = AJS.$(this),
                K = AJS.$(B.dropDown, this),
                I = AJS.$(B.trigger, this),
                J = AJS.dropDown(K, B)[0];
            AJS.$.extend(J, {
                trigger: I
            });
            F(I, L, K, J);
            I.click(function (M) {
                A(M, I, K, J)
            });
            C.push(J)
        })
    }
    return C
};
AJS.dropDown.Ajax = function (C) {
    var B, A = {
        cache: true
    };
    AJS.$.extend(A, C || {});
    B = AJS.dropDown.Standard.call(this, A);
    AJS.$(B).each(function () {
        var D = this;
        AJS.$.extend(D, {
            getAjaxOptions: function (E) {
                var F = function (G) {
                        if (A.formatResults) {
                            G = A.formatResults(G)
                        }
                        if (A.cache) {
                            D.cache.set(D.getAjaxOptions(), G)
                        }
                        D.refreshSuccess(G)
                    };
                if (A.ajaxOptions) {
                    if (AJS.$.isFunction(A.ajaxOptions)) {
                        return AJS.$.extend(A.ajaxOptions.call(D), {
                            success: F
                        })
                    } else {
                        return AJS.$.extend(A.ajaxOptions, {
                            success: F
                        })
                    }
                }
                return AJS.$.extend(E, {
                    success: F
                })
            },
            refreshSuccess: function (E) {
                this.$.html(E)
            },
            cache: function () {
                var E = {};
                return {
                    get: function (F) {
                        var G = F.data || "";
                        return E[(F.url + G).replace(/[\?\&]/gi, "")]
                    },
                    set: function (F, G) {
                        var H = F.data || "";
                        E[(F.url + H).replace(/[\?\&]/gi, "")] = G
                    },
                    reset: function () {
                        E = {}
                    }
                }
            }(),
            show: function (E) {
                return function (F) {
                    if (A.cache && !! D.cache.get(D.getAjaxOptions())) {
                        D.refreshSuccess(D.cache.get(D.getAjaxOptions()));
                        E.call(D)
                    } else {
                        AJS.$(AJS.$.ajax(D.getAjaxOptions())).throbber({
                            target: D.$,
                            end: function () {
                                D.reset()
                            }
                        });
                        E.call(D);
                        D.shadow.hide();
                        if (D.iframeShim) {
                            D.iframeShim.hide()
                        }
                    }
                }
            }(D.show),
            resetCache: function () {
                D.cache.reset()
            }
        });
        D.addCallback("refreshSuccess", function () {
            D.reset()
        })
    });
    return B
};
AJS.$.fn.dropDown = function (B, A) {
    B = (B || "Standard").replace(/^([a-z])/, function (C) {
        return C.toUpperCase()
    });
    return AJS.dropDown[B].call(this, A)
};
(function () {
    AJS.icons = AJS.icons || {};
    AJS.icons.addIcon = function (B, C) {
        AJS.icons[B] = function (E, D) {
            return A(C, E, D)
        }
    };
    AJS.icons.addIcon.init = function () {
        var D = this.className.split(" "),
            B = D.length,
            C = this.className.match(/(^|\s)size-(\d+)(\s|$)/);
        C = C && +C[2];
        while (B--) {
            if (D[B] != "addIcon" && D[B] in AJS.icons) {
                AJS.icons[D[B]](this, C)
            }
        }
    };

    function A(E, C, B) {
        B = B || 24;
        var D = Raphael([C, B + 1, B + 1].concat(E));
        D.scale(B / 24, B / 24, 0, 0)
    }
})();
AJS.$(function () {
    AJS.$(".svg-icon").each(AJS.icons.addIcon.init)
});
AJS.icons.addIcon("generic", [{
    stroke: "none",
    fill: "#999",
    type: "path",
    path: "M22.465,8.464c1.944,1.944,1.944,5.126,0,7.07l-6.93,6.93c-1.944,1.945-5.126,1.945-7.07,0l-6.929-6.93c-1.945-1.943-1.945-5.125,0-7.07l6.929-6.93c1.944-1.944,5.126-1.944,7.07,0L22.465,8.464z"
}, {
    type: "path",
    stroke: "none",
    fill: "90-#999996-#a1a19f:20-#b8b8b7:70-#ccc",
    path: "M9.172,2.242L9.172,2.242l-6.929,6.93C1.491,9.923,1.077,10.927,1.077,12c0,1.072,0.414,2.076,1.166,2.828l6.929,6.93c0.751,0.752,1.756,1.166,2.828,1.166s2.076-0.414,2.828-1.166l6.93-6.93c0.751-0.752,1.165-1.756,1.165-2.828c0-1.072-0.414-2.076-1.165-2.828l-6.93-6.93C13.269,0.682,10.731,0.682,9.172,2.242z"
}, {
    type: "path",
    stroke: "none",
    fill: "270-#999996-#a1a19f:20-#b8b8b7:70-#ccc",
    path: "M7.181,5.869 7.181,17.95 16.974,17.95 16.974,9.205 13.638,5.869"
}, {
    type: "path",
    stroke: "none",
    fill: "#fff",
    path: "M12.724,9.619v-2.75H8.181V16.95h7.793v-6.832h-2.75C12.946,10.119,12.724,9.894,12.724,9.619zM13.724,7.369c0,0.521,0,1.32,0,1.75c0.428,0,1.229,0,1.75,0L13.724,7.369z"
}]);
AJS.icons.addIcon("error", [{
    type: "path",
    stroke: "none",
    fill: "#c00",
    path: "M7.857,22L2,16.143 2,7.857 7.857,1.999 16.143,1.999 22,7.857 22,16.143 16.143,22z"
}, {
    type: "path",
    stroke: "none",
    fill: "90-#c00-#d50909-#ed2121-#f33",
    path: "M8.271,2.999C7.771,3.5,3.501,7.77,3,8.271c0,0.708,0,6.748,0,7.457c0.501,0.5,4.771,4.77,5.271,5.271c0.708,0,6.749,0,7.457,0c0.501-0.502,4.771-4.771,5.271-5.271c0-0.709,0-6.749,0-7.457c-0.501-0.501-4.771-4.771-5.271-5.272C15.021,2.999,8.979,2.999,8.271,2.999z"
}, {
    type: "rect",
    x: 5.318,
    y: 9.321,
    fill: "270-#c00-#d50909-#ed2121-#f33",
    stroke: "none",
    width: 13.363,
    height: 5.356
}, {
    type: "rect",
    x: 6.318,
    y: 10.321,
    fill: "#fff",
    stroke: "none",
    width: 11.363,
    height: 3.356
}]);
AJS.icons.addIcon("success", [{
    type: "path",
    stroke: "none",
    path: "M22,18.801C22,20.559,20.561,22,18.799,22H5.201C3.439,22,2,20.559,2,18.801V5.199C2,3.44,3.439,2,5.201,2h13.598C20.561,2,22,3.44,22,5.199V18.801z",
    fill: "#393"
}, {
    type: "path",
    path: "M5.201,3C3.987,3,3,3.986,3,5.199v13.602C3,20.014,3.987,21,5.201,21h13.598C20.013,21,21,20.014,21,18.801V5.199C21,3.986,20.013,3,18.799,3H5.201z",
    stroke: "none",
    fill: "90-#393-#33a23c-#3c6"
}, {
    type: "path",
    path: "M10.675,12.158c-0.503-0.57-1.644-1.862-1.644-1.862l-3.494,2.833l3.663,5.313l4.503,1.205L17.73,4.624l-4.361-0.056C13.369,4.568,11.424,10.047,10.675,12.158z",
    stroke: "none",
    fill: "270-#393-#33a23c-#3c6"
}, {
    type: "path",
    path: "M14.072,5.577 11.05,14.092 8.917,11.677 6.886,13.324 9.815,17.57 12.997,18.422 16.432,5.607",
    stroke: "none",
    fill: "#fff"
}]);
AJS.icons.addIcon("hint", [{
    type: "path",
    path: "M22.465,8.464c1.944,1.944,1.944,5.126,0,7.07l-6.93,6.93c-1.944,1.945-5.126,1.945-7.07,0l-6.929-6.93c-1.945-1.943-1.945-5.125,0-7.07l6.929-6.93c1.944-1.944,5.126-1.944,7.07,0L22.465,8.464z",
    stroke: "none",
    fill: "#009898"
}, {
    type: "path",
    path: "M9.172,2.242L9.172,2.242l-6.929,6.93C1.491,9.923,1.077,10.927,1.077,12c0,1.072,0.414,2.076,1.166,2.828l6.929,6.93c0.751,0.752,1.756,1.166,2.828,1.166s2.076-0.414,2.828-1.166l6.93-6.93c0.751-0.752,1.165-1.756,1.165-2.828c0-1.072-0.414-2.076-1.165-2.828l-6.93-6.93C13.269,0.682,10.731,0.682,9.172,2.242z",
    stroke: "none",
    fill: "270-#099-#00a2a2-#00baba-#0cc"
}, {
    type: "path",
    path: "M12,5.077c-2.679,0-4.857,2.179-4.857,4.857c0,1.897,0.741,2.864,1.337,3.639c0.385,0.502,0.662,0.863,0.761,1.443l0.045,0.264v2.25c0,0.854,0.693,1.547,1.546,1.547h2.338c0.852,0,1.545-0.693,1.545-1.547v-2.254l0.044-0.258c0.1-0.582,0.377-0.943,0.762-1.443c0.596-0.777,1.338-1.743,1.338-3.641C16.857,7.255,14.679,5.077,12,5.077z",
    stroke: "none",
    fill: "270-#099-#00a2a2-#00baba-#0cc"
}, {
    type: "path",
    path: "M10.227,14.849c-0.331-1.936-2.084-2.197-2.084-4.915c0-2.131,1.727-3.857,3.857-3.857c2.13,0,3.857,1.727,3.857,3.857c0,2.717-1.754,2.979-2.085,4.915H10.227z M10.285,15.849v1.682c0,0.301,0.246,0.547,0.546,0.547h2.338c0.3,0,0.545-0.246,0.545-0.547v-1.682H10.285z",
    stroke: "none",
    fill: "#fff"
}]);
AJS.icons.addIcon("info", [{
    type: "circle",
    cx: 12,
    cy: 12,
    r: 10,
    stroke: "none",
    fill: "#06c"
}, {
    type: "path",
    path: "M3,12c0,4.962,4.037,9,9,9s9-4.038,9-9s-4.037-9-9-9S3,7.037,3,12z",
    stroke: "none",
    fill: "90-#06c-#006FD5-#0087ED-#0099FF"
}, {
    type: "path",
    path: "M9.409,7.472c0,0.694,0.282,1.319,0.729,1.785c-0.288,0-0.729,0-0.729,0v9.425h5.182V9.257c0,0-0.44,0-0.729,0c0.446-0.466,0.729-1.09,0.729-1.785c0-1.429-1.162-2.591-2.591-2.591S9.409,6.043,9.409,7.472z",
    stroke: "none",
    fill: "270-#06c-#006FD5-#0087ED-#0099FF"
}, {
    type: "path",
    path: "M13.591,10.257v7.425h-3.182v-7.425H13.591z M12,9.063c0.879,0,1.591-0.712,1.591-1.591S12.879,5.881,12,5.881s-1.591,0.712-1.591,1.591S11.121,9.063,12,9.063z",
    stroke: "none",
    fill: "#fff"
}]);
AJS.icons.addIcon("warning", [{
    type: "path",
    path: "M8.595,4.368c1.873-3.245,4.938-3.245,6.811,0c1.873,3.245,4.938,8.554,6.812,11.798c1.874,3.244,0.342,5.898-3.405,5.898c-3.746,0-9.876,0-13.624,0c-3.746,0-5.278-2.654-3.405-5.898C3.656,12.922,6.721,7.613,8.595,4.368z",
    stroke: "none",
    fill: "#f90"
}, {
    type: "path",
    path: "M9.461,4.868L2.649,16.666c-0.72,1.246-0.863,2.371-0.404,3.166s1.504,1.232,2.943,1.232h13.624c1.439,0,2.485-0.438,2.944-1.232s0.315-1.92-0.405-3.166L14.539,4.868C13.82,3.622,12.918,2.935,12,2.935S10.181,3.621,9.461,4.868z",
    stroke: "none",
    fill: "90-#f90-#ffa209-#ffba21-#fc3"
}, {
    type: "path",
    path: "M9.274,6.187c0,0,0.968,9.68,0.986,9.862c-0.532,0.476-0.881,1.148-0.881,1.916c0,1.433,1.165,2.598,2.597,2.598c1.433,0,2.598-1.165,2.598-2.598c0-0.77-0.351-1.441-0.883-1.918c0.018-0.184,0.988-9.86,0.988-9.86H9.274z",
    stroke: "none",
    fill: "270-#f90-#ffa209-#ffba21-#fc3"
}, {
    type: "path",
    path: "M11.177,15.171l-0.798-7.984h3.194l-0.8,7.984H11.177z M11.976,16.368c-0.882,0-1.597,0.716-1.597,1.597c0,0.883,0.715,1.598,1.597,1.598c0.881,0,1.598-0.715,1.598-1.598C13.573,17.084,12.856,16.368,11.976,16.368z",
    stroke: "none",
    fill: "#fff"
}]);
AJS.icons.addIcon("close", [{
    type: "path",
    path: "M15.535,12l4.95-4.95c0.977-0.977,0.977-2.559,0-3.536s-2.56-0.977-3.536,0L12,8.464l-4.95-4.95c-0.977-0.977-2.559-0.977-3.536,0s-0.977,2.559,0,3.536L8.464,12l-4.95,4.95c-0.977,0.977-0.977,2.559,0,3.535s2.559,0.977,3.536,0L12,15.535l4.949,4.949c0.977,0.977,2.56,0.977,3.536,0s0.977-2.559,0-3.535L15.535,12z",
    stroke: "none",
    fill: "#999"
}, {
    type: "path",
    path: "M18.718,20.217c-0.401,0-0.777-0.156-1.062-0.439L12,14.121l-5.657,5.656c-0.284,0.283-0.66,0.439-1.061,0.439c-0.4,0-0.777-0.156-1.061-0.439c-0.283-0.283-0.439-0.66-0.439-1.061s0.156-0.777,0.439-1.061L9.878,12L4.222,6.343c-0.283-0.284-0.439-0.66-0.439-1.061c0-0.4,0.156-0.777,0.439-1.061c0.284-0.283,0.66-0.439,1.061-0.439c0.401,0,0.777,0.156,1.061,0.439L12,9.878l5.656-5.657c0.284-0.283,0.66-0.439,1.062-0.439c0.4,0,0.776,0.156,1.061,0.439c0.283,0.284,0.439,0.66,0.439,1.061c0,0.401-0.156,0.777-0.439,1.061L14.121,12l5.657,5.657c0.283,0.283,0.439,0.66,0.439,1.061s-0.156,0.777-0.439,1.061C19.494,20.061,19.118,20.217,18.718,20.217L18.718,20.217z",
    stroke: "none",
    fill: "90-#999996-#a1a19f-#b8b8b7-#ccc"
}]);
(function (A) {
    AJS.InlineDialog = function (T, H, K, I) {
        var R = A.extend(false, AJS.InlineDialog.opts, I);
        var E;
        var J;
        var b;
        var N = false;
        var S = false;
        var Z = false;
        var a;
        var P;
        var B = A('<div id="inline-dialog-' + H + '" class="aui-inline-dialog"><div class="contents"></div><div id="arrow-' + H + '" class="arrow"></div></div>');
        var G = A("#arrow-" + H, B);
        var Y = B.find(".contents");
        Y.css("width", R.width + "px");
        Y.mouseover(function (c) {
            clearTimeout(J);
            B.unbind("mouseover")
        }).mouseout(function () {
            W()
        });
        var V = function () {
                if (!E) {
                    E = {
                        popup: B,
                        hide: function () {
                            W(0)
                        },
                        id: H,
                        show: function () {
                            Q()
                        },
                        reset: function () {
                            var r;
                            var g;
                            var p;
                            var h = -7;
                            var k;
                            var f;
                            var q = P.target.offset();
                            var l = parseInt(P.target.css("padding-left")) + parseInt(P.target.css("padding-right"));
                            var n = P.target.width() + l;
                            var d = q.left + n / 2;
                            var j = (window.pageYOffset || document.documentElement.scrollTop) + A(window).height();
                            var c = 10;

                            function o(u, y, v, x, t, s, w) {
                                u.css({
                                    left: y,
                                    right: v,
                                    top: x
                                });
                                if (window.Raphael) {
                                    if (!u.arrowCanvas) {
                                        u.arrowCanvas = Raphael("arrow-" + H, 16, 16)
                                    }
                                    var z = "M0,8L8,0,16,8";
                                    if (w) {
                                        z = "M0,8L8,16,16,8"
                                    }
                                    u.arrowCanvas.path(z).attr({
                                        fill: "#fff",
                                        stroke: "#bbb"
                                    })
                                }
                                G.css({
                                    position: "absolute",
                                    left: t,
                                    right: "auto",
                                    top: s
                                })
                            }
                            p = q.top + P.target.height() + R.offsetY;
                            r = q.left + R.offsetX;
                            var e = q.top > B.height();
                            var i = (p + B.height()) < j;
                            f = (!i && e) || (R.onTop && e);
                            var m = A(window).width() - (r + R.width + c);
                            if (f) {
                                p = q.top - B.height() - 8;
                                h = B.height() - 9;
                                if (AJS.$.browser.msie) {
                                    h = B.height() - 10
                                }
                            }
                            k = d - r;
                            if (R.isRelativeToMouse) {
                                if (m < 0) {
                                    g = c;
                                    r = "auto";
                                    k = a.x - (A(window).width() - R.width)
                                } else {
                                    r = a.x - 20;
                                    g = "auto";
                                    k = a.x - r
                                }
                            } else {
                                if (m < 0) {
                                    g = c;
                                    r = "auto";
                                    k = d - (A(window).width() - R.width)
                                } else {
                                    if (R.width <= n / 2) {
                                        k = R.width / 2;
                                        r = d - R.width / 2
                                    }
                                }
                            }
                            o(B, r, g, p, k, h, f);
                            B.fadeIn(R.fadeTime, function () {});
                            if (B.shadow) {
                                B.shadow.remove()
                            }
                            B.shadow = Raphael.shadow(0, 0, Y.width(), Y.height(), {
                                target: B[0]
                            }).hide().fadeIn(R.fadeTime);
                            if (AJS.$.browser.msie) {
                                if (A("#inline-dialog-shim-" + H).length == 0) {
                                    A(B).prepend(A('<iframe class = "inline-dialog-shim" id="inline-dialog-shim-' + H + '" frameBorder="0" src="javascript:false;"></iframe>'))
                                }
                                A("#inline-dialog-shim-" + H).css({
                                    width: Y.outerWidth(),
                                    height: Y.outerHeight()
                                })
                            }
                        }
                    }
                }
                return E
            };
        var Q = function () {
                if (B.is(":visible")) {
                    return
                }
                b = setTimeout(function () {
                    if (!Z || !S) {
                        return
                    }
                    A(T).addClass("active");
                    N = true;
                    F();
                    AJS.InlineDialog.current = V();
                    AJS.$(document).trigger("showLayer", ["inlineDialog", V()]);
                    V().reset()
                }, R.showDelay)
            };
        var W = function (c) {
                S = false;
                if (N) {
                    c = (c == null) ? R.hideDelay : c;
                    clearTimeout(J);
                    clearTimeout(b);
                    if (c != null) {
                        J = setTimeout(function () {
                            U();
                            A(T).removeClass("active");
                            B.fadeOut(R.fadeTime, function () {
                                R.hideCallback.call(B[0].popup)
                            });
                            B.shadow.remove();
                            B.shadow = null;
                            B.arrowCanvas.remove();
                            B.arrowCanvas = null;
                            N = false;
                            S = false;
                            AJS.$(document).trigger("hideLayer", ["inlineDialog", V()]);
                            AJS.InlineDialog.current = null;
                            if (!R.cacheContent) {
                                Z = false;
                                O = false
                            }
                        }, c)
                    }
                }
            };
        var X = function (f, c) {
                R.upfrontCallback.call({
                    popup: B,
                    hide: function () {
                        W(0)
                    },
                    id: H,
                    show: function () {
                        Q()
                    }
                });
                B.each(function () {
                    if (typeof this.popup != "undefined") {
                        this.popup.hide()
                    }
                });
                if (R.closeOthers) {
                    AJS.$(".aui-inline-dialog").each(function () {
                        this.popup.hide()
                    })
                }
                if (!f) {
                    a = {
                        x: T.offset().left,
                        y: T.offset().top
                    };
                    P = {
                        target: T
                    }
                } else {
                    a = {
                        x: f.pageX,
                        y: f.pageY
                    };
                    P = {
                        target: A(f.target)
                    }
                }
                if (!N) {
                    clearTimeout(b)
                }
                S = true;
                var d = function () {
                        O = false;
                        Z = true;
                        R.initCallback.call({
                            popup: B,
                            hide: function () {
                                W(0)
                            },
                            id: H,
                            show: function () {
                                Q()
                            }
                        });
                        Q()
                    };
                if (!O) {
                    O = true;
                    if (A.isFunction(K)) {
                        K(Y, c, d)
                    } else {
                        AJS.$.get(K, function (g, e, h) {
                            Y.html(R.responseHandler(g, e, h));
                            Z = true;
                            R.initCallback.call({
                                popup: B,
                                hide: function () {
                                    W(0)
                                },
                                id: H,
                                show: function () {
                                    Q()
                                }
                            });
                            Q()
                        })
                    }
                }
                clearTimeout(J);
                if (!N) {
                    Q()
                }
                return false
            };
        B[0].popup = V();
        var O = false;
        var M = false;
        var L = function () {
                if (!M) {
                    A(R.container).append(B);
                    M = true
                }
            };
        if (R.onHover) {
            if (R.useLiveEvents) {
                A(T).live("mousemove", function (c) {
                    L();
                    X(c, this)
                }).live("mouseout", function () {
                    W()
                })
            } else {
                A(T).mousemove(function (c) {
                    L();
                    X(c, this)
                }).mouseout(function () {
                    W()
                })
            }
        } else {
            if (!R.noBind) {
                if (R.useLiveEvents) {
                    A(T).live("click", function (c) {
                        L();
                        X(c, this);
                        return false
                    }).live("mouseout", function () {
                        W()
                    })
                } else {
                    A(T).click(function (c) {
                        L();
                        X(c, this);
                        return false
                    }).mouseout(function () {
                        W()
                    })
                }
            }
        }
        var D = false;
        var C = H + ".inline-dialog-check";
        var F = function () {
                if (!D) {
                    A("body").bind("click." + C, function (d) {
                        var c = A(d.target);
                        if (c.closest("#inline-dialog-" + H + " .contents").length === 0) {
                            W(0)
                        }
                    });
                    D = true
                }
            };
        var U = function () {
                if (D) {
                    A("body").unbind("click." + C)
                }
                D = false
            };
        B.show = function (c) {
            if (c) {
                c.stopPropagation()
            }
            L();
            X(null, this)
        };
        B.hide = function () {
            W(0)
        };
        B.refresh = function () {
            if (N) {
                V().reset()
            }
        };
        B.getOptions = function () {
            return R
        };
        return B
    };
    AJS.InlineDialog.opts = {
        onTop: false,
        responseHandler: function (C, B, D) {
            return C
        },
        closeOthers: true,
        isRelativeToMouse: false,
        onHover: false,
        useLiveEvents: false,
        noBind: false,
        fadeTime: 100,
        hideDelay: 10000,
        showDelay: 0,
        width: 300,
        offsetX: 0,
        offsetY: 10,
        container: "body",
        cacheContent: true,
        hideCallback: function () {},
        initCallback: function () {},
        upfrontCallback: function () {}
    }
})(jQuery);
AJS.warnAboutFirebug = function (B) {
    if (!AJS.Cookie.read("COOKIE_FB_WARNING") && window.console && window.console.firebug) {
        if (!B) {
            B = "Firebug is known to cause performance problems with Atlassian products. Try disabling it, if you notice any issues."
        }
        var A = AJS.$("<div id='firebug-warning'><p>" + B + "</p><a class='close'>Close</a></div>");
        AJS.$(".close", A).click(function () {
            A.slideUp("fast");
            AJS.Cookie.save("COOKIE_FB_WARNING", "true")
        });
        A.prependTo(AJS.$("body"))
    }
};
AJS.inlineHelp = function () {
    AJS.$(".icon-inline-help").click(function () {
        var A = AJS.$(this).siblings(".field-help");
        if (A.hasClass("hidden")) {
            A.removeClass("hidden")
        } else {
            A.addClass("hidden")
        }
    })
};
(function () {
    AJS.messages = {
        setup: function () {
            AJS.messages.createMessage("generic");
            AJS.messages.createMessage("error");
            AJS.messages.createMessage("warning");
            AJS.messages.createMessage("info");
            AJS.messages.createMessage("success");
            AJS.messages.createMessage("hint");
            AJS.messages.makeCloseable()
        },
        makeCloseable: function (A) {
            AJS.$(A || "div.aui-message.closeable").each(function () {
                var C = AJS.$(this),
                    B = AJS.$('<span class="aui-icon icon-close"></span>').click(function () {
                        C.closeMessage()
                    });
                C.append(B);
                B.each(AJS.icons.addIcon.init)
            })
        },
        template: '<div class="aui-message {type} {closeable} {shadowed}"><p class="title"><span class="aui-icon icon-{type}"></span><strong>{title}</strong></p>{body}</div><!-- .aui-message -->',
        createMessage: function (A) {
            AJS.messages[A] = function (B, C) {
                if (!C) {
                    C = B;
                    B = "#aui-message-bar"
                }
                C.closeable = (C.closeable == false) ? false : true;
                C.shadowed = (C.shadowed == false) ? false : true;
                AJS.$(B).append(AJS.template(this.template).fill({
                    type: A,
                    closeable: C.closeable ? "closeable" : "",
                    shadowed: C.shadowed ? "shadowed" : "",
                    title: C.title || "",
                    "body:html": C.body || ""
                })).find(".svg-icon:empty").each(AJS.icons.addIcon.init);
                C.closeable && AJS.messages.makeCloseable(AJS.$(B).find("div.aui-message.closeable"))
            }
        }
    };
    AJS.$.fn.closeMessage = function () {
        var A = AJS.$(this);
        if (A.hasClass("aui-message", "closeable")) {
            A.trigger("messageClose", [this]).remove()
        }
    };
    AJS.$(function () {
        AJS.messages.setup()
    })
})();
(function () {
    AJS.tables = AJS.tables || {};
    AJS.tables.rowStriping = function () {
        var B = AJS.$("table.aui");
        for (var A = 0, C = B.length; A < C; A++) {
            AJS.$("tbody tr:odd", B[A]).addClass("zebra")
        }
    };
    AJS.$(AJS.tables.rowStriping)
})();
(function () {
    var B, E, C = /#.*/,
        D = "active-tab",
        A = "active-pane";
    AJS.tabs = {
        setup: function () {
            B = AJS.$("div.aui-tabs");
            for (var F = 0, G = B.length; F < G; F++) {
                E = AJS.$("ul.tabs-menu", B[F]);
                AJS.$("a", E).click(function (H) {
                    AJS.tabs.change(AJS.$(this), H);
                    H && H.preventDefault()
                })
            }
        },
        change: function (G, H) {
            var F = AJS.$(G.attr("href").match(C)[0]);
            F.addClass(A).siblings().removeClass(A);
            G.parent("li.menu-item").addClass(D).siblings().removeClass(D);
            G.trigger("tabSelect", {
                tab: G,
                pane: F
            })
        }
    };
    AJS.$(AJS.tabs.setup)
})();
AJS.template = (function (G) {
    var K = /\{([^\}]+)\}/g,
        D = /(?:(?:^|\.)(.+?)(?=\[|\.|$|\()|\[('|")(.+?)\2\])(\(\))?/g,
        M = /[<>"'&]/g,
        H = /([^\\])'/g,
        F = function (Q, P, R, N) {
            var O = R;
            P.replace(D, function (U, T, S, W, V) {
                T = T || W;
                if (O) {
                    if (T + ":html" in O) {
                        O = O[T + ":html"];
                        N = true
                    } else {
                        if (T in O) {
                            O = O[T]
                        }
                    }
                    if (V && typeof O == "function") {
                        O = O()
                    }
                }
            });
            if (O == null || O == R) {
                O = Q
            }
            O = String(O);
            if (!N) {
                O = E.escape(O)
            }
            return O
        },
        I = function (N) {
            return "&#" + N.charCodeAt() + ";"
        },
        C = function (N) {
            this.template = this.template.replace(K, function (P, O) {
                return F(P, O, N, true)
            });
            return this
        },
        L = function (N) {
            this.template = this.template.replace(K, function (P, O) {
                return F(P, O, N)
            });
            return this
        },
        B = function () {
            return this.template
        };
    var E = function (O) {
            function N() {
                return N.template
            }
            N.template = String(O);
            N.toString = N.valueOf = B;
            N.fill = L;
            N.fillHtml = C;
            return N
        },
        A = {},
        J = [];
    E.load = function (N) {
        N = String(N);
        if (!A.hasOwnProperty(N)) {
            J.length >= 1000 && delete A[J.shift()];
            J.push(N);
            A[N] = G("script[title='" + N.replace(H, "$1\\'") + "']")[0].text
        }
        return this(A[N])
    };
    E.escape = function (N) {
        return String(N).replace(M, I)
    };
    return E
})(window.jQuery);
AJS.whenIType = function (D) {
    var A, E = function (F) {
            F = F.toString();
            jQuery(document).bind("keypress", F, function () {
                if (!AJS.popup.current && A) {
                    A()
                }
            });
            jQuery(document).bind("keypress keyup", F, function (G) {
                G.preventDefault()
            })
        },
        B = function (F) {
            var H = jQuery(F),
                I = H.attr("title") || "",
                G = D.split("");
            if (H.data("kbShortcutAppended")) {
                C(H, G, I);
                return
            }
            I += " ( " + AJS.params.keyType + " '" + G.shift() + "'";
            jQuery.each(G, function () {
                I += " " + AJS.params.keyThen + " '" + this + "'"
            });
            I += " )";
            H.attr("title", I);
            H.data("kbShortcutAppended", true)
        },
        C = function (G, F, H) {
            H = H.replace(/\)$/, " OR ");
            H += "'" + F.shift() + "'";
            jQuery.each(F, function () {
                H += " " + AJS.params.keyThen + " '" + this + "'"
            });
            H += " )";
            G.attr("title", H)
        };
    E(D);
    return {
        moveToNextItem: function (F) {
            A = function () {
                var H, G = jQuery(F),
                    I = jQuery(F + ".focused");
                if (!A.blurHandler) {
                    jQuery(document).one("keypress", function (J) {
                        if (J.keyCode === jQuery.ui.keyCode.ESCAPE && I) {
                            I.removeClass("focused")
                        }
                    })
                }
                if (I.length === 0) {
                    I = jQuery(F).eq(0)
                } else {
                    I.removeClass("focused");
                    H = jQuery.inArray(I.get(0), G);
                    if (H < G.length - 1) {
                        H = H + 1;
                        I = G.eq(H)
                    } else {
                        I.removeClass("focused");
                        I = jQuery(F).eq(0)
                    }
                }
                if (I && I.length > 0) {
                    I.addClass("focused");
                    I.moveTo();
                    I.find("a:first").focus()
                }
            }
        },
        moveToPrevItem: function (F) {
            A = function () {
                var H, G = jQuery(F),
                    I = jQuery(F + ".focused");
                if (!A.blurHandler) {
                    jQuery(document).one("keypress", function (J) {
                        if (J.keyCode === jQuery.ui.keyCode.ESCAPE && I) {
                            I.removeClass("focused")
                        }
                    })
                }
                if (I.length === 0) {
                    I = jQuery(F + ":last")
                } else {
                    I.removeClass("focused");
                    H = jQuery.inArray(I.get(0), G);
                    if (H > 0) {
                        H = H - 1;
                        I = G.eq(H)
                    } else {
                        I.removeClass("focused");
                        I = jQuery(F + ":last")
                    }
                }
                if (I && I.length > 0) {
                    I.addClass("focused");
                    I.moveTo();
                    I.find("a:first").focus()
                }
            }
        },
        click: function (F) {
            B(F);
            A = function () {
                var G = jQuery(F);
                if (G.length > 0) {
                    G.click()
                }
            }
        },
        goTo: function (F) {
            A = function () {
                window.location.href = F
            }
        },
        followLink: function (F) {
            B(F);
            A = function () {
                var G = jQuery(F);
                if (G.length > 0 && G.attr("nodeName").toLowerCase() === "a") {
                    window.location.href = G.attr("href")
                }
            }
        },
        execute: function (F) {
            A = function () {
                F()
            }
        },
        moveToAndClick: function (F) {
            B(F);
            A = function () {
                var G = jQuery(F);
                if (G.length > 0) {
                    G.click();
                    G.moveTo()
                }
            }
        },
        moveToAndFocus: function (F) {
            B(F);
            A = function () {
                var G = jQuery(F);
                if (G.length > 0) {
                    G.focus();
                    G.moveTo()
                }
            }
        },
        or: function (F) {
            E(F);
            return this
        }
    }
};
jQuery(document).bind("iframeAppended", function (B, A) {
    jQuery(A).load(function () {
        var C = jQuery(A).contents();
        C.bind("keyup keydown keypress", function (D) {
            if (jQuery.browser.safari && D.type === "keypress") {
                return
            }
            if (!jQuery(D.target).is(":input")) {
                jQuery(document).trigger(D)
            }
        })
    })
});
AJS.whenIType.fromJSON = function (A) {
    if (A) {
        jQuery.each(A, function (C, D) {
            var B = D.op,
                E = D.param;
            jQuery.each(D.keys, function () {
                if (B === "execute") {
                    E = new Function(E)
                }
                AJS.whenIType(this)[B](E)
            })
        })
    }
};
AJS.toInit(function (D) {
    if (D.browser.msie) {
        var F = D(".aui-toolbar .toolbar-group");
        F.each(function (G, H) {
            D(H).children(":first").addClass("first");
            D(H).children(":last").addClass("last")
        });
        if (parseInt(D.browser.version, 10) == 7) {
            function B() {
                D(".aui-toolbar button").closest(".toolbar-item").addClass("contains-button")
            }
            function C() {
                D(".aui-toolbar .toolbar-split-right").each(function (J, M) {
                    var K = D(M),
                        N = K.closest(".aui-toolbar"),
                        G = N.find(".toolbar-split-left"),
                        I = N.data("leftWidth"),
                        L = N.data("rightWidth");
                    if (!I) {
                        I = G.outerWidth();
                        N.data("leftWidth", I)
                    }
                    if (!L) {
                        L = 0;
                        D(".toolbar-item", M).each(function (P, Q) {
                            L += D(Q).outerWidth()
                        });
                        N.data("rightWidth", L)
                    }
                    var O = N.width(),
                        H = O - I;
                    if (O > L && L > H) {
                        G.addClass("force-split")
                    } else {
                        G.removeClass("force-split")
                    }
                })
            }
            function E() {
                F.each(function (G, H) {
                    var I = 0;
                    D(H).children(".toolbar-item").each(function (K, J) {
                        I += D(this).outerWidth()
                    });
                    D(this).width(I)
                })
            }
            E();
            B();
            var A = false;
            D(window).resize(function () {
                if (A !== false) {
                    clearTimeout(A)
                }
                A = setTimeout(C, 200)
            })
        }
    }
});
(function (A) {
    A.fn.autocomplete = function (B, C, K) {
        K = typeof C == "function" ? C : (typeof K == "function" ? K : function () {});
        C = !isNaN(Number(C)) ? C : 3;
        var J = this;
        J[0].lastSelectedValue = J.val();
        var H = A(document.createElement("ol"));
        var D = J.offset();
        var G = parseInt(A("body").css("border-left-width"));
        H.css({
            position: "absolute",
            width: J.outerWidth() - 2 + "px"
        });
        H.addClass("autocompleter");
        this.after(H);
        H.css({
            margin: (Math.abs(this.offset().left - H.offset().left) >= Math.abs(this.offset().top - H.offset().top)) ? J.outerHeight() + "px 0 0 -" + J.outerWidth() + "px" : "-1px 0 0 0"
        });
        H.hide();

        function F() {
            H.hide();
            A(document).unbind("click", F)
        }
        function E() {
            var L = J.val();
            if (L.length >= C && L != J[0].lastQuery && L != J[0].lastSelectedValue) {
                A.getJSON(B + encodeURI(L), function (P) {
                    var R = "";
                    L = L.toLowerCase();
                    var U = L.split(" ");
                    for (var Q = 0, W = P.length; Q < W; Q++) {
                        var S = false;
                        if (P[Q].fullName && P[Q].username) {
                            var V = P[Q].fullName + " (" + P[Q].username + ")";
                            var M = P[Q].fullName.split(" ");
                            for (var O = 0, T = M.length; O < T; O++) {
                                for (var N = 0; N < U.length; N++) {
                                    if (M[O].toLowerCase().indexOf(U[N]) == 0) {
                                        M[O] = "<strong>" + M[O].substring(0, U[N].length) + "</strong>" + M[O].substring(U[N].length);
                                        S = true
                                    }
                                }
                            }
                            if (!S) {
                                for (var N = 0; N < U.length; N++) {
                                    if (P[Q].username && P[Q].username.toLowerCase().indexOf(U[N]) == 0) {
                                        P[Q].username = "<strong>" + P[Q].username.substring(0, U[N].length) + "</strong>" + P[Q].username.substring(U[N].length)
                                    }
                                }
                            }
                            P[Q].fullName = M.join(" ");
                            R += "<li><span>" + P[Q].fullName + "</span> <span class='username-in-autocomplete-list'>(" + P[Q].username + ")</span><i class='fullDetails'>" + V + "</i><i class='username'>" + P[Q].username + "</i><i class='fullName'>" + P[Q].fullName + "</i></li>"
                        }
                        if (P[Q].status) {
                            R += "<li>" + P[Q].status + "</li>"
                        }
                    }
                    H.html(R);
                    A("li", H).click(function (Y) {
                        Y.stopPropagation();
                        var X = A("i.fullDetails", this).html();
                        I(X)
                    }).hover(function () {
                        A(".focused").removeClass("focused");
                        A(this).addClass("focused")
                    }, function () {});
                    A(document).click(F);
                    H.show()
                });
                J[0].lastQuery = L
            } else {
                if (L.length < C) {
                    F()
                }
            }
        }
        J.keydown(function (M) {
            var L = this;
            if (this.timer) {
                clearTimeout(this.timer)
            }
            var N = {
                "40": function () {
                    var O = A(".focused").removeClass("focused").next();
                    if (O.length) {
                        O.addClass("focused")
                    } else {
                        A(".autocompleter li:first").addClass("focused")
                    }
                },
                "38": function () {
                    var O = A(".focused").removeClass("focused").prev();
                    if (O.length) {
                        O.addClass("focused")
                    } else {
                        A("li:last", H).addClass("focused")
                    }
                },
                "27": function () {
                    F()
                },
                "13": function () {
                    var O = A(".focused i.fullDetails").html();
                    I(O)
                },
                "9": function () {
                    this[13]();
                    setTimeout(function () {
                        L.focus()
                    }, 0)
                }
            };
            if (H.css("display") != "none" && M.keyCode in N) {
                M.preventDefault();
                N[M.keyCode]()
            }
            this.timer = setTimeout(E, 300)
        });

        function I(N) {
            var M = J.val();
            if (N) {
                J[0].lastSelectedValue = N;
                J.val(N);
                var L = {
                    input: J,
                    originalValue: M,
                    value: N,
                    fullName: A(".focused i.fullName").text(),
                    username: A(".focused i.username").text()
                };
                K(L);
                F()
            }
        }
    }
})(jQuery);
jQuery.fn.isDirty = function () {
    var B, A = [];
    window.onbeforeunload = function () {
        var C = window.onbeforeunload;
        if (B !== false) {
            jQuery.each(A, function () {
                if (this.initVal !== AJS.$(this).val()) {
                    B = true;
                    return false
                }
            })
        }
        if (B) {
            window.onbeforeunload = null;
            window.setTimeout(function () {
                jQuery(document).bind("mousemove", function () {
                    window.onbeforeunload = C;
                    jQuery(document).unbind("mousemove", arguments.callee)
                })
            }, 1000);
            B = void(0);
            return AJS.params.dirtyMessage || ""
        }
    };
    return function (D) {
        if (this.length === 0) {
            return
        }
        function C(F) {
            var E = jQuery(this);
            jQuery.fn.isDirty.fieldInFocus = E;
            if (jQuery.inArray(this, A) === -1) {
                this.initVal = E.val();
                A.push(this);
                E.die(F.type, C)
            }
        }
        jQuery(":not(:input)").live("click", function () {
            delete jQuery.fn.isDirty.fieldInFocus
        });
        jQuery(":input[type != hidden]", this.selector).bind("keydown", C).bind("keypress", C).bind("click", C);
        jQuery(D.ignoreUnloadFromElems).live("mousedown", function () {
            B = false
        });
        this.each(function () {
            this.onsubmit = function (E) {
                return function () {
                    B = false;
                    if (E) {
                        return E.apply(this, arguments)
                    }
                }
            }(this.onsubmit);
            AJS.$(this).submit(function () {
                B = false
            })
        });
        return this
    }
}();
(function (A) {
    A.fn.progressBar = function (I, L) {
        var C = this;
        var F = {
            height: "1em",
            showPercentage: true
        };
        var B = A.extend(F, L);
        var J = C.attr("id") + "-incomplete-bar";
        var D = C.attr("id") + "-complete-bar";
        var K = C.attr("id") + "-percent-complete-text";
        if (A("#" + J).length == 0) {
            var E = A(document.createElement("div"));
            E.attr("id", J);
            E.css({
                width: "90%",
                border: "solid 1px #ccc",
                "float": "left",
                "margin-right": "0.5em"
            });
            E.addClass("progress-background-color");
            var G = A(document.createElement("div"));
            G.attr("id", D);
            G.addClass("progress-fill-color");
            G.css({
                height: B.height,
                width: I + "%"
            });
            var H = A(document.createElement("span"));
            H.attr("id", K);
            H.addClass("percent-complete-text");
            H.html(I + "%");
            E.append(G);
            C.append(E);
            if (B.showPercentage) {
                C.append(H)
            }
        } else {
            A("#" + D).css("width", I + "%");
            A("#" + K).html(I + "%")
        }
    }
})(jQuery);
(function (A) {
    if (document.selection) {
        var B = function (C) {
                return C.replace(/\u000D/g, "")
            };
        A.fn.selection = function (F) {
            var E = this[0];
            this.focus();
            if (!E) {
                return false
            }
            if (F == null) {
                return document.selection.createRange().text
            } else {
                var D = E.scrollTop;
                var C = document.selection.createRange();
                C.text = F;
                C.select();
                E.focus();
                E.scrollTop = D
            }
        };
        A.fn.selectionRange = function (C, F) {
            var G = this[0];
            this.focus();
            var I = document.selection.createRange();
            if (C == null) {
                var K = this.val(),
                    J = K.length,
                    E = I.duplicate();
                E.moveToElementText(G);
                E.setEndPoint("StartToEnd", I);
                var D = J - B(E.text).length;
                E.setEndPoint("StartToStart", I);
                var H = J - B(E.text).length;
                if (D != H && K.charAt(D + 1) == "\n") {
                    D += 1
                }
                return {
                    end: D,
                    start: H,
                    text: K.substring(H, D),
                    textBefore: K.substring(0, H),
                    textAfter: K.substring(D)
                }
            } else {
                I.moveToElementText(G);
                I.collapse(true);
                I.moveStart("character", C);
                I.moveEnd("character", F - C);
                I.select()
            }
        }
    } else {
        A.fn.selection = function (E) {
            var D = this[0];
            if (!D) {
                return false
            }
            if (E == null) {
                if (D.setSelectionRange) {
                    return D.value.substring(D.selectionStart, D.selectionEnd)
                } else {
                    return false
                }
            } else {
                var C = D.scrollTop;
                if ( !! D.setSelectionRange) {
                    var F = D.selectionStart;
                    D.value = D.value.substring(0, F) + E + D.value.substring(D.selectionEnd);
                    D.selectionStart = F;
                    D.selectionEnd = F + E.length
                }
                D.focus();
                D.scrollTop = C
            }
        };
        A.fn.selectionRange = function (F, C) {
            if (F == null) {
                var D = {
                    start: this[0].selectionStart,
                    end: this[0].selectionEnd
                };
                var E = this.val();
                D.text = E.substring(D.start, D.end);
                D.textBefore = E.substring(0, D.start);
                D.textAfter = E.substring(D.end);
                return D
            } else {
                this[0].selectionStart = F;
                this[0].selectionEnd = C
            }
        }
    }
    A.fn.wrapSelection = function (C, D) {
        this.selection(C + this.selection() + (D || ""))
    }
})(jQuery);
jQuery.fn.throbber = function (A) {
    return function () {
        var C = [],
            B = {
                isLatentThreshold: 100,
                minThrobberDisplay: 200,
                loadingClass: "loading"
            };
        A(document).ajaxComplete(function (E, D) {
            A(C).each(function (F) {
                if (D === this.get(0)) {
                    this.hideThrobber();
                    C.splice(F, 1)
                }
            })
        });
        return function (F) {
            var E, G, D = function (I, H) {
                    D.t = setTimeout(function () {
                        clearTimeout(D.t);
                        D.t = undefined;
                        I()
                    }, H)
                };
            F = A.extend(B, F || {});
            if (!F.target) {
                return this
            }
            G = jQuery(F.target);
            C.push(A.extend(this, {
                showThrobber: function () {
                    D(function () {
                        if (!E) {
                            G.addClass(F.loadingClass);
                            D(function () {
                                if (E) {
                                    E()
                                }
                            }, F.minThrobberDisplay)
                        }
                    }, F.isLatentThreshold)
                },
                hideThrobber: function () {
                    E = function () {
                        G.removeClass(F.loadingClass);
                        if (F.end) {
                            F.end()
                        }
                    };
                    if (!D.t) {
                        E()
                    }
                }
            }));
            this.showThrobber();
            return this
        }
    }()
}(jQuery);
AJS.copyObject = function (object, deep) {
    var copiedObject = {};
    AJS.$.each(object, function (name, property) {
        if (typeof property !== "object" || property === null || property instanceof AJS.$) {
            copiedObject[name] = property
        } else {
            if (deep !== false) {
                copiedObject[name] = AJS.copyObject(property, deep)
            }
        }
    });
    return copiedObject
};
var JIRA = window.JIRA || {};
/* Function: 	AJS.namespace
 * Purpose:		Create a new namespace
 * Inputs:		string:namespace - namespace string
 *				dom:contect -
 *				object:value - value added to the namespace
 * Returns:					
 */
AJS.namespace = function (namespace, context, value) {
    var names = namespace.split(".");
    context = context || window;
    for (var i = 0, n = names.length - 1; i < n; i++) {
        var x = context[names[i]];
        context = (x != null) ? x : context[names[i]] = {}
    }
    return context[names[i]] = value || {}
};
AJS.canAccessIframe = function (iframe) {
    var $iframe = AJS.$(iframe);
    return !/^(http|https):\/\//.test($iframe.attr("src")) || (AJS.params.baseURL && (AJS.$.trim($iframe.attr("src")).indexOf(AJS.params.baseURL) === 0))
};
(function () {
    function preventScrolling(e) {
        var keyCode = e.keyCode,
            keys = AJS.$.ui.keyCode;
        if (!jQuery(e.target).is("textarea, :text, select") && (keyCode === keys.DOWN || keyCode === keys.UP || keyCode === keys.LEFT || keyCode === keys.RIGHT)) {
            e.preventDefault()
        }
    }
    AJS.disableKeyboardScrolling = function () {
        AJS.$(document).bind("keypress keydown", preventScrolling)
    };
    AJS.enableKeyboardScrolling = function () {
        AJS.$(document).unbind("keypress keydown", preventScrolling)
    }
})();
AJS.$.namespace = function (namespace) {
    return AJS.namespace(namespace)
};
jQuery.noConflict();
jQuery.ajaxSettings.traditional = true;
contextPath = typeof contextPath === "undefined" ? "" : contextPath;
AJS.LEFT = "left";
AJS.RIGHT = "right";
AJS.ACTIVE_CLASS = "active";
AJS.BOX_SHADOW_CLASS = "box-shadow";
AJS.LOADING_CLASS = "loading";
AJS.INTELLIGENT_GUESS = "Intelligent Guess";
AJS.DIRTY_FORM_VALUE = "AJS_DirtyForms_cleanValue";
(function () {
    var SPECIAL_CHARS = /[.*+?|^$()[\]{\\]/g;
    RegExp.escape = function (str) {
        return str.replace(SPECIAL_CHARS, "\\$&")
    }
})();
(function ($) {
    $.readData = function (s) {
        var r = {},
            n = "";
        $(s).children().each(function (i) {
            if (i % 2) {
                r[n] = jQuery.trim($(this).text())
            } else {
                n = jQuery.trim($(this).text())
            }
        }).remove();
        $(s).remove();
        return r
    }
})(jQuery);
String.prototype.escapejQuerySelector = function () {
    return this.replace(/([:.])/g, "\\$1")
};
AJS.trigger = function (event, target) {
    event = new jQuery.Event(event);
    jQuery(target || window.top.document).trigger(event);
    return !event.isDefaultPrevented()
};
jQuery.aop.after({
    target: jQuery,
    method: "append"
}, function (elem) {
    var iframes;
    if (elem.attr("tagName") === "iframe" && AJS.canAccessIframe(elem)) {
        if (!elem.data("iframeAppendedFired")) {
            elem.data("iframeAppendedFired", true);
            jQuery(document).trigger("iframeAppended", elem)
        }
    }
    iframes = jQuery("iframe", elem);
    if (iframes.length > 0) {
        jQuery.each(iframes, function (i) {
            var iframe = iframes.eq(i);
            if (!iframe.data("iframeAppendedFired") && AJS.canAccessIframe(iframe)) {
                iframe.data("iframeAppendedFired", true);
                jQuery(document).trigger("iframeAppended", iframe)
            }
        })
    }
    return elem
});
AJS.isSelenium = function () {
    var detectOn = function (obj) {
            for (var propName in obj) {
                if (propName.indexOf("selenium") != -1) {
                    return true
                }
            }
            return false
        };
    return detectOn(window.location) || detectOn(window)
};
AJS.reloadViaWindowLocation = function (url) {
    var windowReload = function () {
            window.location.reload()
        };
    url = url || window.location.href;
    if (AJS.isSelenium()) {
        windowReload()
    } else {
        var makeHashUrlsUnique = function (url) {
                var MAGIC_PARAM = "jwupdated";
                var hashIndex = url.indexOf("#");
                if (hashIndex == -1) {
                    return url
                }
                var secondsSinceMidnight = function () {
                        var now = new Date();
                        var midnight = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0);
                        var secs = (now.getTime() - midnight.getTime()) / 1000;
                        return Math.max(Math.floor(secs), 1)
                    };
                var firstQuestionMark = url.indexOf("?");
                var magicParamValue = MAGIC_PARAM + "=" + secondsSinceMidnight();
                if (firstQuestionMark == -1) {
                    url = url.replace("#", "?" + magicParamValue + "#")
                } else {
                    if (url.indexOf(MAGIC_PARAM + "=") != -1) {
                        url = url.replace(/(jwupdated=[0-9]+)/, magicParamValue)
                    } else {
                        url = url.replace("?", "?" + magicParamValue + "&")
                    }
                }
                return url
            };
        url = makeHashUrlsUnique(url);
        if (jQuery.browser.webkit && parseInt(jQuery.browser.version) < 533) {
            window.location = url
        } else {
            window.location.replace(url)
        }
    }
};
AJS.extractBodyFromResponse = function (text) {
    var fragment = text.match(/<body[^>]*>([\S\s]*)<\/body[^>]*>/);
    if (fragment && fragment.length > 0) {
        return fragment[1]
    }
    return text
};
(function () {
    var SPECIAL_CHARS = /[&"<]/g;
    AJS.escapeHTML = function (str) {
        return str.replace(SPECIAL_CHARS, replacement)
    };

    function replacement(specialChar) {
        switch (specialChar) {
        case "<":
            return "&lt;";
        case "&":
            return "&amp;";
        default:
            return "&quot;"
        }
    }
})();

function tryIt(f, defaultVal) {
    try {
        return f()
    } catch (ex) {
        return defaultVal
    }
}
function begetObject(obj) {
    var f = function () {};
    f.prototype = obj;
    return new f
}
function submitOnEnter(e) {
    if (e.keyCode == 13 && e.target.form && !e.ctrlKey && !e.shiftKey) {
        jQuery(e.target.form).submit();
        return false
    }
    return true
}
function submitOnCtrlEnter(e) {
    if (e.ctrlKey && e.target.form && (e.keyCode == 13 || e.keyCode == 10)) {
        jQuery(e.target.form).submit();
        return false
    }
    return true
}
function getMultiSelectValues(selectObject) {
    var selectedValues = "";
    for (var i = 0; i < selectObject.length; i++) {
        if (selectObject.options[i].selected) {
            if (selectObject.options[i].value && selectObject.options[i].value.length > 0) {
                selectedValues = selectedValues + " " + selectObject.options[i].value
            }
        }
    }
    return selectedValues
}
function getMultiSelectValuesAsArray(selectObject) {
    var selectedValues = new Array();
    for (var i = 0; i < selectObject.length; i++) {
        if (selectObject.options[i].selected) {
            if (selectObject.options[i].value && selectObject.options[i].value.length > 0) {
                selectedValues[selectedValues.length] = selectObject.options[i].value
            }
        }
    }
    return selectedValues
}
function arrayContains(array, value) {
    for (var i = 0; i < array.length; i++) {
        if (array[i] == value) {
            return true
        }
    }
    return false
}
function addClassName(elementId, classNameToAdd) {
    var elem = document.getElementById(elementId);
    if (elem) {
        elem.className = elem.className + " " + classNameToAdd
    }
}
function removeClassName(elementId, classNameToRemove) {
    var elem = document.getElementById(elementId);
    if (elem) {
        elem.className = (" " + elem.className + " ").replace(" " + classNameToRemove + " ", " ")
    }
}
function getEscapedFieldValue(id) {
    var e = document.getElementById(id);
    if (e.value) {
        return id + "=" + encodeURIComponent(e.value)
    } else {
        return ""
    }
}
function getEscapedFieldValues(ids) {
    var s = "";
    for (var i = 0; i < ids.length; i++) {
        s = s + "&" + getEscapedFieldValue(ids[i])
    }
    return s
}
var GuiPrefs = {
    toggleVisibility: function (elementId) {
        var elem = document.getElementById(elementId);
        if (elem) {
            if (readFromConglomerateCookie("jira.conglomerate.cookie", elementId, "1") == "1") {
                elem.style.display = "none";
                removeClassName(elementId + "header", "headerOpened");
                addClassName(elementId + "header", "headerClosed");
                saveToConglomerateCookie("jira.conglomerate.cookie", elementId, "0")
            } else {
                elem.style.display = "";
                removeClassName(elementId + "header", "headerClosed");
                addClassName(elementId + "header", "headerOpened");
                eraseFromConglomerateCookie("jira.conglomerate.cookie", elementId)
            }
        }
    }
};

function toggle(elementId) {
    GuiPrefs.toggleVisibility(elementId)
}
function toggleDivsWithCookie(elementShowId, elementHideId) {
    var elementShow = document.getElementById(elementShowId);
    var elementHide = document.getElementById(elementHideId);
    if (elementShow.style.display == "none") {
        elementHide.style.display = "none";
        elementShow.style.display = "block";
        saveToConglomerateCookie("jira.viewissue.cong.cookie", elementShowId, "1");
        saveToConglomerateCookie("jira.viewissue.cong.cookie", elementHideId, "0")
    } else {
        elementShow.style.display = "none";
        elementHide.style.display = "block";
        saveToConglomerateCookie("jira.viewissue.cong.cookie", elementHideId, "1");
        saveToConglomerateCookie("jira.viewissue.cong.cookie", elementShowId, "0")
    }
}
function restoreDivFromCookie(elementId, cookieName, defaultValue) {
    if (defaultValue == null) {
        defaultValue = "1"
    }
    var elem = document.getElementById(elementId);
    if (elem) {
        if (readFromConglomerateCookie(cookieName, elementId, defaultValue) != "1") {
            elem.style.display = "none";
            removeClassName(elementId + "header", "headerOpened");
            addClassName(elementId + "header", "headerClosed")
        } else {
            elem.style.display = "";
            removeClassName(elementId + "header", "headerClosed");
            addClassName(elementId + "header", "headerOpened")
        }
    }
}
function restore(elementId) {
    restoreDivFromCookie(elementId, "jira.conglomerate.cookie", "1")
}
function saveToConglomerateCookie(cookieName, name, value) {
    var cookieValue = getCookieValue(cookieName);
    cookieValue = addOrAppendToValue(name, value, cookieValue);
    saveCookie(cookieName, cookieValue, 365)
}
function readFromConglomerateCookie(cookieName, name, defaultValue) {
    var cookieValue = getCookieValue(cookieName);
    var value = getValueFromCongolmerate(name, cookieValue);
    if (value != null) {
        return value
    }
    return defaultValue
}
function eraseFromConglomerateCookie(cookieName, name) {
    saveToConglomerateCookie(cookieName, name, "")
}
function getValueFromCongolmerate(name, cookieValue) {
    if (cookieValue == null) {
        cookieValue = ""
    }
    var eq = name + "=";
    var cookieParts = cookieValue.split("|");
    for (var i = 0; i < cookieParts.length; i++) {
        var cp = cookieParts[i];
        while (cp.charAt(0) == " ") {
            cp = cp.substring(1, cp.length)
        }
        if (cp.indexOf(name) == 0) {
            return cp.substring(eq.length, cp.length)
        }
    }
    return null
}
function addOrAppendToValue(name, value, cookieValue) {
    var newCookieValue = "";
    if (cookieValue == null) {
        cookieValue = ""
    }
    var cookieParts = cookieValue.split("|");
    for (var i = 0; i < cookieParts.length; i++) {
        var cp = cookieParts[i];
        if (cp != "") {
            while (cp.charAt(0) == " ") {
                cp = cp.substring(1, cp.length)
            }
            if (cp.indexOf(name) != 0) {
                newCookieValue += cp + "|"
            }
        }
    }
    if (value != null && value != "") {
        var pair = name + "=" + value;
        if ((newCookieValue.length + pair.length) < 4020) {
            newCookieValue += pair
        }
    }
    return newCookieValue
}
function getCookieValue(name) {
    var eq = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") {
            c = c.substring(1, c.length)
        }
        if (c.indexOf(eq) == 0) {
            return unescape(c.substring(eq.length, c.length))
        }
    }
    return null
}
function saveCookie(name, value, days) {
    if (typeof contextPath === "undefined") {
        return
    }
    var path = contextPath;
    if (!path) {
        path = "/"
    }
    var ex;
    if (days) {
        var d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        ex = "; expires=" + d.toGMTString()
    } else {
        ex = ""
    }
    document.cookie = name + "=" + escape(value) + ex + ";path=" + path
}
function readCookie(name, defaultValue) {
    var cookieVal = getCookieValue(name);
    if (cookieVal != null) {
        return cookieVal
    }
    if (defaultValue) {
        saveCookie(name, defaultValue, 365);
        return defaultValue
    } else {
        return null
    }
}
function eraseCookie(name) {
    saveCookie(name, "", -1)
}
function recolourSimpleTableRows(tableId) {
    recolourTableRows(tableId, "rowNormal", "rowAlternate", tableId + "_empty")
}
function recolourTableRows(tableId, rowNormal, rowAlternate, emptyTableId) {
    var tbl = document.getElementById(tableId);
    var emptyTable = document.getElementById(emptyTableId);
    var alternate = false;
    var rowsFound = 0;
    var rows = tbl.rows;
    var firstVisibleRow = null;
    var lastVisibleRow = null;
    if (AJS.$(tbl).hasClass("aui")) {
        rowNormal = "";
        rowAlternate = "zebra"
    }
    for (var i = 1; i < rows.length; i++) {
        var row = rows[i];
        if (row.style.display != "none") {
            if (!alternate) {
                row.className = rowNormal
            } else {
                row.className = rowAlternate
            }
            rowsFound++;
            alternate = !alternate
        }
        if (row.style.display != "none") {
            if (firstVisibleRow == null) {
                firstVisibleRow = row
            }
            lastVisibleRow = row
        }
    }
    if (firstVisibleRow != null) {
        firstVisibleRow.className = firstVisibleRow.className + " first-row"
    }
    if (lastVisibleRow != null) {
        lastVisibleRow.className = lastVisibleRow.className + " last-row"
    }
    if (emptyTable) {
        if (rowsFound == 0) {
            tbl.style.display = "none";
            emptyTable.style.display = ""
        } else {
            tbl.style.display = "";
            emptyTable.style.display = "none"
        }
    }
}
function htmlEscape(str) {
    var divE = document.createElement("div");
    divE.appendChild(document.createTextNode(str));
    return divE.innerHTML
}
function atl_token() {
    return jQuery("#atlassian-token").attr("content")
}
function openDateRangePicker(formName, previousFieldName, nextFieldName, fieldId) {
    var previousFieldValue = document.forms[formName].elements[previousFieldName].value;
    var nextFieldValue = document.forms[formName].elements[nextFieldName].value;
    var url = contextPath + "/secure/popups/DateRangePicker.jspa?";
    url += "formName=" + formName + "&";
    url += "previousFieldName=" + escape(previousFieldName) + "&";
    url += "nextFieldName=" + escape(nextFieldName) + "&";
    url += "previousFieldValue=" + escape(previousFieldValue) + "&";
    url += "nextFieldValue=" + escape(nextFieldValue) + "&";
    url += "fieldId=" + escape(fieldId);
    var vWinUsers = window.open(url, "DateRangePopup", "status=no,resizable=yes,top=100,left=200,width=580,height=400,scrollbars=yes");
    vWinUsers.opener = self;
    vWinUsers.focus()
}
function show_calendar2(formName, fieldName) {
    var form = document.forms[formName];
    var element = form.elements[fieldName];
    var vWinCal = window.open(contextPath + "/secure/popups/Calendar.jspa?form=" + formName + "&field=" + fieldName + "&value=" + escape(element.value) + "&decorator=none", "Calendar", "width=230,height=170,status=no,resizable=yes,top=220,left=200");
    vWinCal.opener = self;
    vWinCal.focus()
};
(function () {
    begetObject = function (obj) {
        var f = function () {};
        f.prototype = obj;
        return new f()
    };
    var initializing = false,
        fnTest = /xyz/.test(function () {
            xyz
        }) ? /\b_super\b/ : /.*/;
    this.Class = function () {};
    Class.extend = function () {
        var prop;
        var _super = this.prototype;
        if (arguments.length > 1) {
            var interfaces = AJS.$.makeArray(arguments);
            prop = interfaces.pop();
            var completeInterface;
            AJS.$.each(interfaces, function (i, inter) {
                if (completeInterface) {
                    completeInterface = completeInterface.extend(inter)
                } else {
                    completeInterface = inter
                }
            });
            return completeInterface.extend(this.prototype).extend(prop)
        } else {
            prop = arguments[0]
        }
        initializing = true;
        var prototype = new this();
        initializing = false;
        for (var name in prop) {
            if (prototype[name] = typeof prop[name] == "function" && typeof _super[name] == "function" && fnTest.test(prop[name])) {
                prototype[name] = (function (name, fn) {
                    return function () {
                        var tmp = this._super;
                        this._super = _super[name];
                        var ret = fn.apply(this, arguments);
                        this._super = tmp;
                        return ret
                    }
                })(name, prop[name])
            } else {
                if (typeof _super[name] === "object") {
                    var newObj = begetObject(prop[name]);
                    AJS.$.each(_super[name], function (name, obj) {
                        if (!newObj[name]) {
                            newObj[name] = obj
                        } else {
                            if (typeof newObj[name] === "object") {
                                var newSubObj = begetObject(newObj[name]);
                                AJS.$.each(obj, function (subName, subObj) {
                                    if (!newSubObj[subName]) {
                                        newSubObj[subName] = subObj
                                    }
                                });
                                newObj[name] = newSubObj
                            }
                        }
                    });
                    prototype[name] = newObj
                } else {
                    prototype[name] = prop[name]
                }
            }
        }
        function Class() {
            if (!initializing && this.init) {
                this.init.apply(this, arguments)
            }
        }
        Class.prototype = prototype;
        Class.constructor = Class;
        Class.extend = arguments.callee;
        return Class
    }
})();
AJS.Control = Class.extend({
    INVALID: "INVALID",
    _throwReadOnlyError: function (property) {
        new Error(this.CLASS_SIGNATURE + ": Sorry [" + property + "] is a read-only property")
    },
    _assignEvents: function (group, target) {
        var key, handlers;
        var instance = this;
        this._activeEvents = this._activeEvents || {};
        if (typeof target === "string") {
            key = group + "/" + target;
            if (this._activeEvents[key]) {
                return
            }
            handlers = this._activeEvents[key] = {};
            AJS.$.each(this._events[group], function (eventType, handler) {
                handlers[eventType] = function (event) {
                    handler.call(instance, event, AJS.$(this))
                };
                AJS.$(document).delegate(target, eventType, handlers[eventType])
            })
        } else {
            target = AJS.$(target);
            if (target.length === 0) {
                return
            }
            if (this._activeEvents[group + "/" + target[0][AJS.$.expando]]) {
                return
            }
            handlers = {};
            AJS.$.each(this._events[group], function (eventType, handler) {
                handlers[eventType] = function (event) {
                    handler.call(instance, event, AJS.$(this))
                };
                target.bind(eventType, handlers[eventType])
            });
            key = group + "/" + (target[0] === window ? "window" : target[0][AJS.$.expando]);
            this._activeEvents[key] = handlers
        }
    },
    _unassignEvents: function (group, target) {
        var key, handlers, eventType;
        this._activeEvents = this._activeEvents || {};
        if (typeof target === "string") {
            key = group + "/" + target;
            handlers = this._activeEvents[key];
            if (!handlers) {
                return
            }
            for (eventType in handlers) {
                AJS.$(document).undelegate(target, eventType, handlers[eventType])
            }
        } else {
            target = AJS.$(target);
            if (target.length === 0) {
                return
            }
            key = group + "/" + target[0][AJS.$.expando];
            handlers = this._activeEvents[key];
            if (!handlers) {
                return
            }
            try {
                for (eventType in handlers) {
                    target.unbind(eventType, handlers[eventType])
                }
            } catch (error) {
                var events = AJS.$.data(target[0], "events");
                if (events) {
                    for (eventType in handlers) {
                        var $handlers = events[eventType];
                        if (!$handlers) {
                            continue
                        }
                        var i = $handlers.length;
                        while (i--) {
                            if ($handlers[i].handler === handlers[eventType]) {
                                $handlers.splice(i, 1);
                                if ($handlers.length === 0) {
                                    delete events[eventType]
                                }
                                break
                            }
                        }
                    }
                }
            }
        }
        delete this._activeEvents[key]
    },
    _isValidInput: function () {
        return true
    },
    _handleKeyEvent: function (e) {
        var instance = this;
        if (instance._isValidInput(e)) {
            var SpecialKey = JIRA.Keyboard.SpecialKey,
                shortcut = JIRA.Keyboard.shortcutEntered(e);
            if (shortcut) {
                if (instance.keys[shortcut]) {
                    instance.keys[shortcut].call(instance, e);
                    return
                } else {
                    if ((shortcut === SpecialKey.BACKSPACE || shortcut === SpecialKey.DELETE) && instance.keys.onEdit) {
                        instance.keys.onEdit.call(instance, e);
                        return
                    }
                }
            }
            var character = JIRA.Keyboard.characterEntered(e);
            if (character && instance.keys.onEdit) {
                instance.keys.onEdit.call(instance, e, character)
            }
        }
    },
    getCustomEventName: function (methodName) {
        return (this.CLASS_SIGNATURE || "") + "_" + methodName
    },
    _getCustomEventArgs: function () {
        return [this]
    },
    trigger: function (event) {
        return AJS.trigger(event, this)
    },
    _supportsBoxShadow: function () {
        var s = document.body.style;
        return s.WebkitBoxShadow !== undefined || s.MozBoxShadow !== undefined || s.boxShadow !== undefined
    },
    _setOptions: function (options) {
        var element, optionsFromDOM;
        options = options || {};
        if (options instanceof AJS.$ || typeof options === "string" || (typeof options === "object" && options.nodeName)) {
            options = {
                element: options
            }
        }
        element = AJS.$(options.element);
        optionsFromDOM = element.getOptionsFromAttributes();
        this.options = AJS.$.extend(true, this._getDefaultOptions(options), optionsFromDOM, options);
        if (element.length === 0) {
            return this.INVALID
        }
        return undefined
    },
    getCaret: function (node) {
        var startIndex = node.selectionStart;
        if (startIndex >= 0) {
            return (node.selectionEnd > startIndex) ? -1 : startIndex
        }
        if (document.selection) {
            var textRange1 = document.selection.createRange();
            if (textRange1.text.length === 0) {
                var textRange2 = textRange1.duplicate();
                textRange2.moveToElementText(node);
                textRange2.setEndPoint("EndToStart", textRange1);
                return textRange2.text.length
            }
        }
        return -1
    },
    _render: function () {
        var i, name = arguments[0],
            args = [];
        for (i = 1; i < arguments.length; i++) {
            args.push(arguments[i])
        }
        return this._renders[name].apply(this, args)
    }
});
AJS.Descriptor = Class.extend({
    init: function (properties) {
        if (this._validate(properties)) {
            this.properties = AJS.$.extend(this._getDefaultOptions(), properties)
        }
    },
    allProperties: function () {
        return this.properties
    },
    _validate: function (properties) {
        if (this.REQUIRED_PROPERTIES) {
            AJS.$.each(this.REQUIRED_PROPERTIES, function (name) {
                if (typeof properties[name] === "undefined") {
                    throw new Error("AJS.Descriptor: expected property [" + name + "] but was undefined")
                }
            })
        }
        return true
    }
});
JIRA.Keyboard = {};
(function ($) {
    var _keyCodeToEnum = {},
        _enumToKeyCode = {},
        _keyCodeToIsAscii = {};
    var SpecialKey = JIRA.Keyboard.SpecialKey = {
        BACKSPACE: specialKey("backspace", 8, true),
        TAB: specialKey("tab", 9, true),
        RETURN: specialKey("return", 13, true),
        SHIFT: specialKey("shift", 16),
        CTRL: specialKey("ctrl", 17),
        ALT: specialKey("alt", 18),
        PAUSE: specialKey("pause", 19),
        CAPS_LOCK: specialKey("capslock", 20),
        ESC: specialKey("esc", 27, true),
        SPACE: specialKey("space", 32, true),
        PAGE_UP: specialKey("pageup", 33),
        PAGE_DOWN: specialKey("pagedown", 34),
        END: specialKey("end", 35),
        HOME: specialKey("home", 36),
        LEFT: specialKey("left", 37),
        UP: specialKey("up", 38),
        RIGHT: specialKey("right", 39),
        DOWN: specialKey("down", 40),
        INSERT: specialKey("insert", 45),
        DELETE: specialKey("del", 46),
        F1: specialKey("f1", 112),
        F2: specialKey("f2", 113),
        F3: specialKey("f3", 114),
        F4: specialKey("f4", 115),
        F5: specialKey("f5", 116),
        F6: specialKey("f6", 117),
        F7: specialKey("f7", 118),
        F8: specialKey("f8", 119),
        F9: specialKey("f9", 120),
        F10: specialKey("f10", 121),
        F11: specialKey("f11", 122),
        F12: specialKey("f12", 123),
        NUMLOCK: specialKey("numlock", 144),
        SCROLL: specialKey("scroll", 145),
        META: specialKey("meta", 224)
    };

    function specialKey(name, keyCode, isAscii) {
        _keyCodeToEnum[keyCode] = name;
        _enumToKeyCode[name] = keyCode;
        if (isAscii) {
            _keyCodeToIsAscii[keyCode] = true
        }
        return name
    }
    SpecialKey.eventType = function () {
        return $.browser.mozilla ? "keypress" : "keydown"
    };
    SpecialKey.fromKeyCode = function (keyCode) {
        return _keyCodeToEnum[keyCode]
    };
    SpecialKey.toKeyCode = function (specialKey) {
        return _enumToKeyCode[specialKey]
    };
    SpecialKey.isAscii = function (keyCode) {
        return !!_keyCodeToIsAscii[keyCode]
    };
    SpecialKey.isSpecialKey = function (keyName) {
        return !!SpecialKey.toKeyCode(keyName)
    };

    function originalEvent(e) {
        return e.originalEvent || e
    }
    JIRA.Keyboard.characterEntered = function (keypressEvent) {
        var e = originalEvent(keypressEvent);
        if (e.type === "keypress") {
            var characterCode = characterCodeForKeypress(e);
            if (characterCode !== null && (!SpecialKey.isAscii(characterCode) || SpecialKey.fromKeyCode(characterCode) === SpecialKey.SPACE)) {
                return String.fromCharCode(characterCode)
            }
        }
        return undefined
    };

    function characterCodeForKeypress(keypressEvent) {
        var e = originalEvent(keypressEvent);
        if (e.which == null) {
            return e.keyCode
        } else {
            if (e.which != 0 && e.charCode != 0) {
                return e.which
            } else {
                return null
            }
        }
    }
    JIRA.Keyboard.specialKeyEntered = function (e) {
        e = originalEvent(e);
        if ($.browser.mozilla) {
            if (e.type === "keypress") {
                var characterCode = characterCodeForKeypress(e);
                if (characterCode === null) {
                    return SpecialKey.fromKeyCode(e.keyCode)
                } else {
                    if (SpecialKey.isAscii(characterCode)) {
                        return SpecialKey.fromKeyCode(characterCode)
                    }
                }
            }
        } else {
            if (e.type !== "keypress") {
                return SpecialKey.fromKeyCode(e.keyCode)
            }
        }
        return undefined
    };

    function keyEntered(e) {
        e = originalEvent(e);
        var special = JIRA.Keyboard.specialKeyEntered(e);
        if (special) {
            return special
        } else {
            if ($.browser.mozilla) {
                if (e.type === "keypress") {
                    var characterCode = characterCodeForKeypress(e);
                    if (characterCode !== null) {
                        return String.fromCharCode(characterCode).toLowerCase()
                    }
                }
            } else {
                if (e.type !== "keypress") {
                    return String.fromCharCode(e.keyCode).toLowerCase()
                }
            }
        }
        return undefined
    }
    JIRA.Keyboard.shortcutEntered = function (e) {
        e = originalEvent(e);
        if (e.type === JIRA.Keyboard.SpecialKey.eventType()) {
            var specialKey = JIRA.Keyboard.specialKeyEntered(e),
                modifiers = "";
            if (e.altKey && specialKey !== SpecialKey.ALT) {
                modifiers += modifier(SpecialKey.ALT)
            }
            if (e.ctrlKey && specialKey !== SpecialKey.CTRL) {
                modifiers += modifier(SpecialKey.CTRL)
            }
            if (e.metaKey && !e.ctrlKey && specialKey !== SpecialKey.META) {
                modifiers += modifier(SpecialKey.META)
            }
            if (e.shiftKey && specialKey !== SpecialKey.SHIFT) {
                modifiers += modifier(SpecialKey.SHIFT)
            }
            if (specialKey) {
                return modifiers + specialKey
            } else {
                if (modifiers.length > 0 && modifiers !== "shift+") {
                    var key = keyEntered(e);
                    if (key) {
                        return modifiers + key
                    }
                }
            }
        }
        return undefined
    };

    function modifier(modifier) {
        return modifier + "+"
    }
})(AJS.$);
(function ($) {
    JIRA.Mouse = {};
    var MotionDetector = JIRA.Mouse.MotionDetector = function () {
            this.reset()
        };
    MotionDetector.prototype.reset = function () {
        this._handler = null;
        this._x = null;
        this._y = null;
        this.moved = false
    };
    MotionDetector.prototype.wait = function (eventHandler) {
        var instance = this;
        if (!instance._handler) {
            this.reset();
            $(window.top.document).bind("mousemove", instance._handler = function (e) {
                if (!instance._x && !instance._y) {
                    instance._x = e.pageX;
                    instance._y = e.pageY
                } else {
                    if (!(e.pageX === instance._x && e.pageY === instance._y)) {
                        instance.unbind();
                        instance.moved = true;
                        if (eventHandler) {
                            eventHandler.call(this, e)
                        }
                    }
                }
            })
        }
    };
    MotionDetector.prototype.unbind = function () {
        if (this._handler) {
            $(window.top.document).unbind("mousemove", this._handler);
            this.reset()
        }
    }
})(AJS.$);
AJS.describeBrowser = function (userAgent) {
    userAgent = userAgent || navigator.userAgent;
    var match = jQuery.uaMatch(userAgent),
        browser = match.browser,
        version = match.version.replace(/\.0$/, ""),
        classNames = [];
    classNames.push(browser);
    if (browser === "msie") {
        classNames.push(browser + "-" + version);
        version = parseInt(version);
        while (version > 6) {
            --version;
            classNames.push(browser + "-gt-" + version)
        }
    }
    jQuery("html").addClass(classNames.join(" "))
};
jQuery.fn.getOptionsFromAttributes = function () {
    var options = {};
    if (this.length) {
        jQuery.each(this[0].attributes, function () {
            var map, nodeValue = this.nodeValue,
                target = options;
            if (/^data-/.test(this.nodeName)) {
                map = this.nodeName.replace(/^data-/, "").split("."), AJS.$.each(map, function (i, propertyName) {
                    propertyName = propertyName.replace(/([a-z])-([a-z])/gi, function (entireMatch, firstMatch, secondMatch) {
                        return firstMatch + secondMatch.toUpperCase()
                    });
                    propertyName = propertyName.replace(/_([a-z]+)/gi, function (entireMatch, firstMatch) {
                        return firstMatch.toUpperCase()
                    });
                    if (i === map.length - 1) {
                        target[propertyName] = nodeValue.match(/^(tru|fals)e$/i) ? nodeValue.toLowerCase() == "true" : nodeValue
                    } else {
                        if (!target[propertyName]) {
                            target[propertyName] = {}
                        }
                    }
                    target = target[propertyName]
                })
            }
        })
    }
    return options
};
JIRA.SmartAjax = {};
(function ($) {
    JIRA.SmartAjax.SmartAjaxResult = function (xhr, requestId, statusText, data, successful, errorThrown) {
        var status = tryIt(function () {
            return xhr.status
        }, 0);
        var result = {
            successful: successful,
            status: status,
            statusText: statusText,
            errorThrown: errorThrown,
            readyState: xhr.readyState,
            hasData: data != null && data.length > 0,
            data: data,
            xhr: xhr,
            aborted: xhr.aborted,
            requestId: requestId
        };
        result.toString = function () {
            return "{\n" + "successful  : " + this.successful + ",\n" + "status      : " + this.status + ",\n" + "statusText  : " + this.statusText + ",\n" + "hasData     : " + this.hasData + ",\n" + "readyState  : " + this.readyState + ",\n" + "requestId   : " + this.requestId + ",\n" + "aborted     : " + this.aborted + ",\n" + "}"
        };
        return result
    };
    JIRA.SmartAjax.SmartAjaxResult.ERROR = "error";
    JIRA.SmartAjax.SmartAjaxResult.TIMEOUT = "timeout";
    JIRA.SmartAjax.SmartAjaxResult.NOTMODIFIED = "notmodified";
    JIRA.SmartAjax.SmartAjaxResult.PARSEERROR = "parseerror";
    JIRA.SmartAjax.makeRequest = function (ajaxOptions) {
        var _smartAjaxResult = {};
        var log = function (calltype, requestId, msg) {
                if (AJS.log) {
                    var id = requestId ? "[" + requestId + "] " : " ";
                    AJS.log("ajax" + id + calltype + " : " + msg)
                }
            };
        var generateRequestId = function () {
                var now = new Date();
                var midnight = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0);
                var ms = (now.getTime() - midnight.getTime());
                return Math.max(Math.floor(ms), 1)
            };
        var errorHandler = function (xhr, statusText, errorThrown, smartAjaxResult) {
                if (!smartAjaxResult) {
                    var data = tryIt(function () {
                        return xhr.responseText
                    }, "");
                    smartAjaxResult = _smartAjaxResult = new JIRA.SmartAjax.SmartAjaxResult(xhr, _requestId, statusText, data, false, errorThrown)
                }
                log("error", smartAjaxResult.requestId, smartAjaxResult);
                if ($.isFunction(ajaxOptions.error)) {
                    ajaxOptions.error(xhr, statusText, errorThrown, smartAjaxResult)
                }
            };
        var successHandler = function (data, statusText, xhr) {
                if (xhr.status < 100) {
                    _smartAjaxResult = new JIRA.SmartAjax.SmartAjaxResult(xhr, _requestId, JIRA.SmartAjax.SmartAjaxResult.ERROR, "", false);
                    errorHandler(xhr, JIRA.SmartAjax.SmartAjaxResult.ERROR, undefined, _smartAjaxResult);
                    return
                }
                _smartAjaxResult = new JIRA.SmartAjax.SmartAjaxResult(xhr, _requestId, statusText, data, true);
                log("success", _smartAjaxResult.requestId, _smartAjaxResult);
                if ($.isFunction(ajaxOptions.success)) {
                    ajaxOptions.success(data, statusText, xhr, _smartAjaxResult)
                }
            };
        var completeHandler = function (xhr, textStatus) {
                if ($.isFunction(ajaxOptions.complete)) {
                    ajaxOptions.complete(xhr, textStatus, _smartAjaxResult)
                }
            };
        var ourAjaxOptions = {};
        for (var x in ajaxOptions) {
            ourAjaxOptions[x] = ajaxOptions[x]
        }
        ourAjaxOptions.error = errorHandler;
        ourAjaxOptions.success = successHandler;
        ourAjaxOptions.complete = completeHandler;
        var xhr = $.ajax(ourAjaxOptions);
        var _requestId = generateRequestId();
        try {
            xhr.abort = function (oldabort) {
                return function () {
                    log("aborted", _requestId, "");
                    xhr.aborted = true;
                    if ($.isFunction(oldabort)) {
                        try {
                            oldabort.call(xhr)
                        } catch (ex) {}
                    }
                }
            }(xhr.abort)
        } catch (ex) {}
        log("started", _requestId, "" + ourAjaxOptions.url);
        return xhr
    };
    JIRA.SmartAjax.buildDialogErrorContent = function (smartAjaxResult, noHeader) {
        var fourHundredClass = Math.floor(smartAjaxResult.status / 100);
        if (smartAjaxResult.hasData && fourHundredClass != 4) {
            return wrapDialogErrorContent(AJS.extractBodyFromResponse(smartAjaxResult.data))
        } else {
            var errMsg = buildRawHttpErrorMessage(smartAjaxResult);
            return buildDialogAjaxErrorMessage(errMsg, noHeader)
        }
    };
    JIRA.SmartAjax.buildSimpleErrorContent = function (smartAjaxResult) {
        return buildRawHttpErrorMessage(smartAjaxResult)
    };

    function buildRawHttpErrorMessage(smartAjaxResult) {
        var AJS = window.top.AJS;
        var errMsg;
        if (smartAjaxResult.statusText == JIRA.SmartAjax.SmartAjaxResult.TIMEOUT) {
            errMsg = AJS.params.ajaxTimeout
        } else {
            if (smartAjaxResult.status == 401) {
                errMsg = AJS.params.ajaxUnauthorised
            } else {
                if (smartAjaxResult.hasData) {
                    errMsg = AJS.params.ajaxServerError
                } else {
                    errMsg = AJS.params.ajaxCommsError
                }
            }
        }
        return errMsg
    }
    function buildDialogAjaxErrorMessage(errorMessage, noHeader) {
        var errorContent = '<div class="warningBox">' + "<p>" + errorMessage + "</p>" + "<p>" + AJS.params.ajaxErrorCloseDialog + "</p>" + "</div>";
        if (!noHeader) {
            errorContent = "<h1>" + AJS.params.ajaxErrorDialogHeading + "</h1>" + errorContent
        }
        return wrapDialogErrorContent(errorContent)
    }
    function wrapDialogErrorContent(content) {
        var $container = $('<div class="ajaxerror"/>');
        $container.append(content);
        return $container
    }
})(AJS.$);
AJS.$(function () {
    AJS.$.ajaxSetup({
        timeout: 60000,
        async: true,
        cache: false,
        global: true
    })
});
AJS.namespace("jira.ajax", null, JIRA.SmartAjax);
(function () {
    var $doc = jQuery(document);

    function getWindow() {
        var topWindow = window;
        try {
            while (topWindow.parent.window !== topWindow.window && topWindow.parent.AJS) {
                topWindow = topWindow.parent
            }
        } catch (error) {}
        return topWindow
    }
    function getLayer(instance) {
        return instance.$layer || instance.$popup || instance.$ || instance.popup
    }
    function listenForLayerEvents($doc) {
        $doc.bind("showLayer", function (e, type, item) {
            if (item && item.id && item.id.indexOf("user-hover-dialog") >= 0) {
                return
            }
            var topWindow = getWindow().AJS;
            if (topWindow.currentLayerItem && item !== topWindow.currentLayerItem && topWindow.currentLayerItem.type !== "popup") {
                topWindow.currentLayerItem.hide()
            }
            if (item) {
                topWindow.currentLayerItem = item;
                topWindow.currentLayerItem.type = type
            }
        }).bind("hideLayer", function (e, type, item) {
            if (!item || item.id && item.id.indexOf("user-hover-dialog") >= 0) {
                return
            }
            var topWindow = getWindow().AJS;
            if (topWindow.currentLayerItem) {
                if (topWindow.currentLayerItem === item) {
                    topWindow.currentLayerItem = null
                } else {
                    if (jQuery.contains(getLayer(item), getLayer(topWindow.currentLayerItem))) {
                        topWindow.currentLayerItem.hide()
                    }
                }
            }
        }).bind("hideAllLayers", function () {
            var topWindow = getWindow().AJS;
            if (topWindow.currentLayerItem) {
                topWindow.currentLayerItem.hide()
            }
        }).click(function (e) {
            var topWindow = getWindow().AJS;
            if (topWindow.currentLayerItem && topWindow.currentLayerItem.type !== "popup") {
                if (topWindow.currentLayerItem._validateClickToClose) {
                    if (topWindow.currentLayerItem._validateClickToClose(e)) {
                        topWindow.currentLayerItem.hide()
                    }
                } else {
                    topWindow.currentLayerItem.hide()
                }
            }
        })
    }
    $doc.bind("iframeAppended", function (e, iframe) {
        iframe = jQuery(iframe);
        iframe.load(function () {
            listenForLayerEvents(iframe.contents())
        })
    });
    listenForLayerEvents($doc)
})();
jQuery.fn.hasFixedParent = function () {
    var hasFixedParent = false;
    this.parents().each(function () {
        if (AJS.$(this).css("position") === "fixed") {
            hasFixedParent = true;
            return false
        }
    });
    return hasFixedParent
};
jQuery.getDocHeight = function () {
    return Math.max(jQuery(document).height(), jQuery(window).height(), document.documentElement.clientHeight)
};
jQuery.os = {};
if (navigator.platform.toLowerCase().indexOf("win") != -1) {
    jQuery.os.windows = true
}
if (navigator.platform.toLowerCase().indexOf("mac") != -1) {
    jQuery.os.mac = true
}
if (navigator.platform.toLowerCase().indexOf("linux") != -1) {
    jQuery.os.linux = true
};
if (!this.JSON) {
    this.JSON = {}
}(function () {
    function f(n) {
        return n < 10 ? "0" + n : n
    }
    if (typeof Date.prototype.toJSON !== "function") {
        Date.prototype.toJSON = function (key) {
            return isFinite(this.valueOf()) ? this.getUTCFullYear() + "-" + f(this.getUTCMonth() + 1) + "-" + f(this.getUTCDate()) + "T" + f(this.getUTCHours()) + ":" + f(this.getUTCMinutes()) + ":" + f(this.getUTCSeconds()) + "Z" : null
        };
        String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function (key) {
            return this.valueOf()
        }
    }
    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap, indent, meta = {
            "\b": "\\b",
            "\t": "\\t",
            "\n": "\\n",
            "\f": "\\f",
            "\r": "\\r",
            '"': '\\"',
            "\\": "\\\\"
        },
        rep;

    function quote(string) {
        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === "string" ? c : "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4)
        }) + '"' : '"' + string + '"'
    }
    function str(key, holder) {
        var i, k, v, length, mind = gap,
            partial, value = holder[key];
        if (value && typeof value === "object" && typeof value.toJSON === "function") {
            value = value.toJSON(key)
        }
        if (typeof rep === "function") {
            value = rep.call(holder, key, value)
        }
        switch (typeof value) {
        case "string":
            return quote(value);
        case "number":
            return isFinite(value) ? String(value) : "null";
        case "boolean":
        case "null":
            return String(value);
        case "object":
            if (!value) {
                return "null"
            }
            gap += indent;
            partial = [];
            if (Object.prototype.toString.apply(value) === "[object Array]") {
                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || "null"
                }
                v = partial.length === 0 ? "[]" : gap ? "[\n" + gap + partial.join(",\n" + gap) + "\n" + mind + "]" : "[" + partial.join(",") + "]";
                gap = mind;
                return v
            }
            if (rep && typeof rep === "object") {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    k = rep[i];
                    if (typeof k === "string") {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ": " : ":") + v)
                        }
                    }
                }
            } else {
                for (k in value) {
                    if (Object.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ": " : ":") + v)
                        }
                    }
                }
            }
            v = partial.length === 0 ? "{}" : gap ? "{\n" + gap + partial.join(",\n" + gap) + "\n" + mind + "}" : "{" + partial.join(",") + "}";
            gap = mind;
            return v
        }
    }
    if (typeof JSON.stringify !== "function") {
        JSON.stringify = function (value, replacer, space) {
            var i;
            gap = "";
            indent = "";
            if (typeof space === "number") {
                for (i = 0; i < space; i += 1) {
                    indent += " "
                }
            } else {
                if (typeof space === "string") {
                    indent = space
                }
            }
            rep = replacer;
            if (replacer && typeof replacer !== "function" && (typeof replacer !== "object" || typeof replacer.length !== "number")) {
                throw new Error("JSON.stringify")
            }
            return str("", {
                "": value
            })
        }
    }
    if (typeof JSON.parse !== "function") {
        JSON.parse = function (text, reviver) {
            var j;

            function walk(holder, key) {
                var k, v, value = holder[key];
                if (value && typeof value === "object") {
                    for (k in value) {
                        if (Object.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v
                            } else {
                                delete value[k]
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value)
            }
            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4)
                })
            }
            if (/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) {
                j = eval("(" + text + ")");
                return typeof reviver === "function" ? walk({
                    "": j
                }, "") : j
            }
            throw new SyntaxError("JSON.parse")
        }
    }
}());

function parseUri(str) {
    var o = parseUri.options,
        m = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
        uri = {},
        i = 14;
    while (i--) {
        uri[o.key[i]] = m[i] || ""
    }
    uri[o.q.name] = {};
    uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
        if ($1) {
            uri[o.q.name][$1] = $2
        }
    });
    return uri
}
parseUri.options = {
    strictMode: false,
    key: ["source", "protocol", "authority", "userInfo", "user", "password", "host", "port", "relative", "path", "directory", "file", "query", "anchor"],
    q: {
        name: "queryKey",
        parser: /(?:^|&)([^&=]*)=?([^&]*)/g
    },
    parser: {
        strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
        loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
    }
};
jQuery.effects || (function ($) {
    $.effects = {};
    $.each(["backgroundColor", "borderBottomColor", "borderLeftColor", "borderRightColor", "borderTopColor", "color", "outlineColor"], function (i, attr) {
        $.fx.step[attr] = function (fx) {
            if (!fx.colorInit) {
                fx.start = getColor(fx.elem, attr);
                fx.end = getRGB(fx.end);
                fx.colorInit = true
            }
            fx.elem.style[attr] = "rgb(" + Math.max(Math.min(parseInt((fx.pos * (fx.end[0] - fx.start[0])) + fx.start[0], 10), 255), 0) + "," + Math.max(Math.min(parseInt((fx.pos * (fx.end[1] - fx.start[1])) + fx.start[1], 10), 255), 0) + "," + Math.max(Math.min(parseInt((fx.pos * (fx.end[2] - fx.start[2])) + fx.start[2], 10), 255), 0) + ")"
        }
    });

    function getRGB(color) {
        var result;
        if (color && color.constructor == Array && color.length == 3) {
            return color
        }
        if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color)) {
            return [parseInt(result[1], 10), parseInt(result[2], 10), parseInt(result[3], 10)]
        }
        if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color)) {
            return [parseFloat(result[1]) * 2.55, parseFloat(result[2]) * 2.55, parseFloat(result[3]) * 2.55]
        }
        if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color)) {
            return [parseInt(result[1], 16), parseInt(result[2], 16), parseInt(result[3], 16)]
        }
        if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color)) {
            return [parseInt(result[1] + result[1], 16), parseInt(result[2] + result[2], 16), parseInt(result[3] + result[3], 16)]
        }
        if (result = /rgba\(0, 0, 0, 0\)/.exec(color)) {
            return colors["transparent"]
        }
        return colors[$.trim(color).toLowerCase()]
    }
    function getColor(elem, attr) {
        var color;
        do {
            color = $.curCSS(elem, attr);
            if (color != "" && color != "transparent" || $.nodeName(elem, "body")) {
                break
            }
            attr = "backgroundColor"
        } while (elem = elem.parentNode);
        return getRGB(color)
    }
    var colors = {
        aqua: [0, 255, 255],
        azure: [240, 255, 255],
        beige: [245, 245, 220],
        black: [0, 0, 0],
        blue: [0, 0, 255],
        brown: [165, 42, 42],
        cyan: [0, 255, 255],
        darkblue: [0, 0, 139],
        darkcyan: [0, 139, 139],
        darkgrey: [169, 169, 169],
        darkgreen: [0, 100, 0],
        darkkhaki: [189, 183, 107],
        darkmagenta: [139, 0, 139],
        darkolivegreen: [85, 107, 47],
        darkorange: [255, 140, 0],
        darkorchid: [153, 50, 204],
        darkred: [139, 0, 0],
        darksalmon: [233, 150, 122],
        darkviolet: [148, 0, 211],
        fuchsia: [255, 0, 255],
        gold: [255, 215, 0],
        green: [0, 128, 0],
        indigo: [75, 0, 130],
        khaki: [240, 230, 140],
        lightblue: [173, 216, 230],
        lightcyan: [224, 255, 255],
        lightgreen: [144, 238, 144],
        lightgrey: [211, 211, 211],
        lightpink: [255, 182, 193],
        lightyellow: [255, 255, 224],
        lime: [0, 255, 0],
        magenta: [255, 0, 255],
        maroon: [128, 0, 0],
        navy: [0, 0, 128],
        olive: [128, 128, 0],
        orange: [255, 165, 0],
        pink: [255, 192, 203],
        purple: [128, 0, 128],
        violet: [128, 0, 128],
        red: [255, 0, 0],
        silver: [192, 192, 192],
        white: [255, 255, 255],
        yellow: [255, 255, 0],
        transparent: [255, 255, 255]
    };
    var classAnimationActions = ["add", "remove", "toggle"],
        shorthandStyles = {
            border: 1,
            borderBottom: 1,
            borderColor: 1,
            borderLeft: 1,
            borderRight: 1,
            borderTop: 1,
            borderWidth: 1,
            margin: 1,
            padding: 1
        };

    function getElementStyles() {
        var style = document.defaultView ? document.defaultView.getComputedStyle(this, null) : this.currentStyle,
            newStyle = {},
            key, camelCase;
        if (style && style.length && style[0] && style[style[0]]) {
            var len = style.length;
            while (len--) {
                key = style[len];
                if (typeof style[key] == "string") {
                    camelCase = key.replace(/\-(\w)/g, function (all, letter) {
                        return letter.toUpperCase()
                    });
                    newStyle[camelCase] = style[key]
                }
            }
        } else {
            for (key in style) {
                if (typeof style[key] === "string") {
                    newStyle[key] = style[key]
                }
            }
        }
        return newStyle
    }
    function filterStyles(styles) {
        var name, value;
        for (name in styles) {
            value = styles[name];
            if (value == null || $.isFunction(value) || name in shorthandStyles || (/scrollbar/).test(name) || (!(/color/i).test(name) && isNaN(parseFloat(value)))) {
                delete styles[name]
            }
        }
        return styles
    }
    function styleDifference(oldStyle, newStyle) {
        var diff = {
            _: 0
        },
            name;
        for (name in newStyle) {
            if (oldStyle[name] != newStyle[name]) {
                diff[name] = newStyle[name]
            }
        }
        return diff
    }
    $.effects.animateClass = function (value, duration, easing, callback) {
        if ($.isFunction(easing)) {
            callback = easing;
            easing = null
        }
        return this.each(function () {
            var that = $(this),
                originalStyleAttr = that.attr("style") || " ",
                originalStyle = filterStyles(getElementStyles.call(this)),
                newStyle, className = that.attr("className");
            $.each(classAnimationActions, function (i, action) {
                if (value[action]) {
                    that[action + "Class"](value[action])
                }
            });
            newStyle = filterStyles(getElementStyles.call(this));
            that.attr("className", className);
            that.animate(styleDifference(originalStyle, newStyle), duration, easing, function () {
                $.each(classAnimationActions, function (i, action) {
                    if (value[action]) {
                        that[action + "Class"](value[action])
                    }
                });
                if (typeof that.attr("style") == "object") {
                    that.attr("style").cssText = "";
                    that.attr("style").cssText = originalStyleAttr
                } else {
                    that.attr("style", originalStyleAttr)
                }
                if (callback) {
                    callback.apply(this, arguments)
                }
            })
        })
    };
    $.fn.extend({
        _addClass: $.fn.addClass,
        addClass: function (classNames, speed, easing, callback) {
            return speed ? $.effects.animateClass.apply(this, [{
                add: classNames
            },
            speed, easing, callback]) : this._addClass(classNames)
        },
        _removeClass: $.fn.removeClass,
        removeClass: function (classNames, speed, easing, callback) {
            return speed ? $.effects.animateClass.apply(this, [{
                remove: classNames
            },
            speed, easing, callback]) : this._removeClass(classNames)
        },
        _toggleClass: $.fn.toggleClass,
        toggleClass: function (classNames, force, speed, easing, callback) {
            if (typeof force == "boolean" || force === undefined) {
                if (!speed) {
                    return this._toggleClass(classNames, force)
                } else {
                    return $.effects.animateClass.apply(this, [(force ? {
                        add: classNames
                    } : {
                        remove: classNames
                    }), speed, easing, callback])
                }
            } else {
                return $.effects.animateClass.apply(this, [{
                    toggle: classNames
                },
                force, speed, easing])
            }
        },
        switchClass: function (remove, add, speed, easing, callback) {
            return $.effects.animateClass.apply(this, [{
                add: add,
                remove: remove
            },
            speed, easing, callback])
        }
    });
    $.extend($.effects, {
        version: "1.8rc3",
        save: function (element, set) {
            for (var i = 0; i < set.length; i++) {
                if (set[i] !== null) {
                    element.data("ec.storage." + set[i], element[0].style[set[i]])
                }
            }
        },
        restore: function (element, set) {
            for (var i = 0; i < set.length; i++) {
                if (set[i] !== null) {
                    element.css(set[i], element.data("ec.storage." + set[i]))
                }
            }
        },
        setMode: function (el, mode) {
            if (mode == "toggle") {
                mode = el.is(":hidden") ? "show" : "hide"
            }
            return mode
        },
        getBaseline: function (origin, original) {
            var y, x;
            switch (origin[0]) {
            case "top":
                y = 0;
                break;
            case "middle":
                y = 0.5;
                break;
            case "bottom":
                y = 1;
                break;
            default:
                y = origin[0] / original.height
            }
            switch (origin[1]) {
            case "left":
                x = 0;
                break;
            case "center":
                x = 0.5;
                break;
            case "right":
                x = 1;
                break;
            default:
                x = origin[1] / original.width
            }
            return {
                x: x,
                y: y
            }
        },
        createWrapper: function (element) {
            if (element.parent().is(".ui-effects-wrapper")) {
                return element.parent()
            }
            var props = {
                width: element.outerWidth(true),
                height: element.outerHeight(true),
                "float": element.css("float")
            },
                wrapper = $("<div></div>").addClass("ui-effects-wrapper").css({
                    fontSize: "100%",
                    background: "transparent",
                    border: "none",
                    margin: 0,
                    padding: 0
                });
            element.wrap(wrapper);
            wrapper = element.parent();
            if (element.css("position") == "static") {
                wrapper.css({
                    position: "relative"
                });
                element.css({
                    position: "relative"
                })
            } else {
                $.extend(props, {
                    position: element.css("position"),
                    zIndex: element.css("z-index")
                });
                $.each(["top", "left", "bottom", "right"], function (i, pos) {
                    props[pos] = element.css(pos);
                    if (isNaN(parseInt(props[pos], 10))) {
                        props[pos] = "auto"
                    }
                });
                element.css({
                    position: "relative",
                    top: 0,
                    left: 0
                })
            }
            return wrapper.css(props).show()
        },
        removeWrapper: function (element) {
            if (element.parent().is(".ui-effects-wrapper")) {
                return element.parent().replaceWith(element)
            }
            return element
        },
        setTransition: function (element, list, factor, value) {
            value = value || {};
            $.each(list, function (i, x) {
                unit = element.cssUnit(x);
                if (unit[0] > 0) {
                    value[x] = unit[0] * factor + unit[1]
                }
            });
            return value
        }
    });

    function _normalizeArguments(effect, options, speed, callback) {
        if (typeof effect == "object") {
            callback = options;
            speed = null;
            options = effect;
            effect = options.effect
        }
        if ($.isFunction(options)) {
            callback = options;
            speed = null;
            options = {}
        }
        if (typeof options == "number" || $.fx.speeds[options]) {
            callback = speed;
            speed = options;
            options = {}
        }
        options = options || {};
        speed = speed || options.duration;
        speed = $.fx.off ? 0 : typeof speed == "number" ? speed : $.fx.speeds[speed] || $.fx.speeds._default;
        callback = callback || options.complete;
        return [effect, options, speed, callback]
    }
    $.fn.extend({
        effect: function (effect, options, speed, callback) {
            var args = _normalizeArguments.apply(this, arguments),
                args2 = {
                    options: args[1],
                    duration: args[2],
                    callback: args[3]
                },
                effectMethod = $.effects[effect];
            return effectMethod && !$.fx.off ? effectMethod.call(this, args2) : this
        },
        _show: $.fn.show,
        show: function (speed) {
            if (!speed || typeof speed == "number" || $.fx.speeds[speed]) {
                return this._show.apply(this, arguments)
            } else {
                var args = _normalizeArguments.apply(this, arguments);
                args[1].mode = "show";
                return this.effect.apply(this, args)
            }
        },
        _hide: $.fn.hide,
        hide: function (speed) {
            if (!speed || typeof speed == "number" || $.fx.speeds[speed]) {
                return this._hide.apply(this, arguments)
            } else {
                var args = _normalizeArguments.apply(this, arguments);
                args[1].mode = "hide";
                return this.effect.apply(this, args)
            }
        },
        __toggle: $.fn.toggle,
        toggle: function (speed) {
            if (!speed || typeof speed == "number" || $.fx.speeds[speed] || typeof speed == "boolean" || $.isFunction(speed)) {
                return this.__toggle.apply(this, arguments)
            } else {
                var args = _normalizeArguments.apply(this, arguments);
                args[1].mode = "toggle";
                return this.effect.apply(this, args)
            }
        },
        cssUnit: function (key) {
            var style = this.css(key),
                val = [];
            $.each(["em", "px", "%", "pt"], function (i, unit) {
                if (style.indexOf(unit) > 0) {
                    val = [parseFloat(style), unit]
                }
            });
            return val
        }
    });
    $.easing.jswing = $.easing.swing;
    $.extend($.easing, {
        def: "easeOutQuad",
        swing: function (x, t, b, c, d) {
            return $.easing[$.easing.def](x, t, b, c, d)
        },
        easeInQuad: function (x, t, b, c, d) {
            return c * (t /= d) * t + b
        },
        easeOutQuad: function (x, t, b, c, d) {
            return -c * (t /= d) * (t - 2) + b
        },
        easeInOutQuad: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) {
                return c / 2 * t * t + b
            }
            return -c / 2 * ((--t) * (t - 2) - 1) + b
        },
        easeInCubic: function (x, t, b, c, d) {
            return c * (t /= d) * t * t + b
        },
        easeOutCubic: function (x, t, b, c, d) {
            return c * ((t = t / d - 1) * t * t + 1) + b
        },
        easeInOutCubic: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) {
                return c / 2 * t * t * t + b
            }
            return c / 2 * ((t -= 2) * t * t + 2) + b
        },
        easeInQuart: function (x, t, b, c, d) {
            return c * (t /= d) * t * t * t + b
        },
        easeOutQuart: function (x, t, b, c, d) {
            return -c * ((t = t / d - 1) * t * t * t - 1) + b
        },
        easeInOutQuart: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) {
                return c / 2 * t * t * t * t + b
            }
            return -c / 2 * ((t -= 2) * t * t * t - 2) + b
        },
        easeInQuint: function (x, t, b, c, d) {
            return c * (t /= d) * t * t * t * t + b
        },
        easeOutQuint: function (x, t, b, c, d) {
            return c * ((t = t / d - 1) * t * t * t * t + 1) + b
        },
        easeInOutQuint: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) {
                return c / 2 * t * t * t * t * t + b
            }
            return c / 2 * ((t -= 2) * t * t * t * t + 2) + b
        },
        easeInSine: function (x, t, b, c, d) {
            return -c * Math.cos(t / d * (Math.PI / 2)) + c + b
        },
        easeOutSine: function (x, t, b, c, d) {
            return c * Math.sin(t / d * (Math.PI / 2)) + b
        },
        easeInOutSine: function (x, t, b, c, d) {
            return -c / 2 * (Math.cos(Math.PI * t / d) - 1) + b
        },
        easeInExpo: function (x, t, b, c, d) {
            return (t == 0) ? b : c * Math.pow(2, 10 * (t / d - 1)) + b
        },
        easeOutExpo: function (x, t, b, c, d) {
            return (t == d) ? b + c : c * (-Math.pow(2, -10 * t / d) + 1) + b
        },
        easeInOutExpo: function (x, t, b, c, d) {
            if (t == 0) {
                return b
            }
            if (t == d) {
                return b + c
            }
            if ((t /= d / 2) < 1) {
                return c / 2 * Math.pow(2, 10 * (t - 1)) + b
            }
            return c / 2 * (-Math.pow(2, -10 * --t) + 2) + b
        },
        easeInCirc: function (x, t, b, c, d) {
            return -c * (Math.sqrt(1 - (t /= d) * t) - 1) + b
        },
        easeOutCirc: function (x, t, b, c, d) {
            return c * Math.sqrt(1 - (t = t / d - 1) * t) + b
        },
        easeInOutCirc: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) {
                return -c / 2 * (Math.sqrt(1 - t * t) - 1) + b
            }
            return c / 2 * (Math.sqrt(1 - (t -= 2) * t) + 1) + b
        },
        easeInElastic: function (x, t, b, c, d) {
            var s = 1.70158;
            var p = 0;
            var a = c;
            if (t == 0) {
                return b
            }
            if ((t /= d) == 1) {
                return b + c
            }
            if (!p) {
                p = d * 0.3
            }
            if (a < Math.abs(c)) {
                a = c;
                var s = p / 4
            } else {
                var s = p / (2 * Math.PI) * Math.asin(c / a)
            }
            return -(a * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p)) + b
        },
        easeOutElastic: function (x, t, b, c, d) {
            var s = 1.70158;
            var p = 0;
            var a = c;
            if (t == 0) {
                return b
            }
            if ((t /= d) == 1) {
                return b + c
            }
            if (!p) {
                p = d * 0.3
            }
            if (a < Math.abs(c)) {
                a = c;
                var s = p / 4
            } else {
                var s = p / (2 * Math.PI) * Math.asin(c / a)
            }
            return a * Math.pow(2, -10 * t) * Math.sin((t * d - s) * (2 * Math.PI) / p) + c + b
        },
        easeInOutElastic: function (x, t, b, c, d) {
            var s = 1.70158;
            var p = 0;
            var a = c;
            if (t == 0) {
                return b
            }
            if ((t /= d / 2) == 2) {
                return b + c
            }
            if (!p) {
                p = d * (0.3 * 1.5)
            }
            if (a < Math.abs(c)) {
                a = c;
                var s = p / 4
            } else {
                var s = p / (2 * Math.PI) * Math.asin(c / a)
            }
            if (t < 1) {
                return -0.5 * (a * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p)) + b
            }
            return a * Math.pow(2, -10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p) * 0.5 + c + b
        },
        easeInBack: function (x, t, b, c, d, s) {
            if (s == undefined) {
                s = 1.70158
            }
            return c * (t /= d) * t * ((s + 1) * t - s) + b
        },
        easeOutBack: function (x, t, b, c, d, s) {
            if (s == undefined) {
                s = 1.70158
            }
            return c * ((t = t / d - 1) * t * ((s + 1) * t + s) + 1) + b
        },
        easeInOutBack: function (x, t, b, c, d, s) {
            if (s == undefined) {
                s = 1.70158
            }
            if ((t /= d / 2) < 1) {
                return c / 2 * (t * t * (((s *= (1.525)) + 1) * t - s)) + b
            }
            return c / 2 * ((t -= 2) * t * (((s *= (1.525)) + 1) * t + s) + 2) + b
        },
        easeInBounce: function (x, t, b, c, d) {
            return c - $.easing.easeOutBounce(x, d - t, 0, c, d) + b
        },
        easeOutBounce: function (x, t, b, c, d) {
            if ((t /= d) < (1 / 2.75)) {
                return c * (7.5625 * t * t) + b
            } else {
                if (t < (2 / 2.75)) {
                    return c * (7.5625 * (t -= (1.5 / 2.75)) * t + 0.75) + b
                } else {
                    if (t < (2.5 / 2.75)) {
                        return c * (7.5625 * (t -= (2.25 / 2.75)) * t + 0.9375) + b
                    } else {
                        return c * (7.5625 * (t -= (2.625 / 2.75)) * t + 0.984375) + b
                    }
                }
            }
        },
        easeInOutBounce: function (x, t, b, c, d) {
            if (t < d / 2) {
                return $.easing.easeInBounce(x, t * 2, 0, c, d) * 0.5 + b
            }
            return $.easing.easeOutBounce(x, t * 2 - d, 0, c, d) * 0.5 + c * 0.5 + b
        }
    })
})(jQuery);
(function () {
    JIRA.parseOptionsFromFieldset = function ($fieldset) {
        var parsedValues = parseFieldset($fieldset, $fieldset);
        $fieldset.remove();
        return parsedValues
    };

    function parseFieldset($fieldset, $parentFieldset) {
        var ret = {};
        $fieldset.children().each(function () {
            var itemValue, $item = jQuery(this);
            if ($item.is("input[type=hidden]")) {
                itemValue = parseValue($item);
                ret[itemValue.id] = itemValue.value
            } else {
                if ($item.is("fieldset")) {
                    ret[$item.attr("title") || $item.attr("id")] = parseFieldset($item, $parentFieldset)
                } else {
                    $item.insertBefore($parentFieldset)
                }
            }
        });
        return ret
    }
    function parseValue($item) {
        var itemValue = {},
            value = $item.val();
        itemValue.id = $item.attr("title") || $item.attr("id");
        itemValue.value = (value.match(/^(tru|fals)e$/i) ? value.toLowerCase() == "true" : value);
        return itemValue
    }
})();
jQuery.fn.handleAccessKeys = function (options) {
    var accessKeyAttr = "accesskey";
    if (jQuery.browser.msie && jQuery.browser.version == "7.0") {
        accessKeyAttr = "accessKey"
    }
    options = options || {};
    this.each(function () {
        var $form = AJS.$(this),
            blackList = [],
            $myAccessKeyElems, $accessKeyElems;
        $accessKeyElems = jQuery("form").not(this).find(":input[" + accessKeyAttr + "], a[" + accessKeyAttr + "]");
        $myAccessKeyElems = jQuery(":input[" + accessKeyAttr + "], a[" + accessKeyAttr + "]", this);
        if (!$form.is("form")) {
            console.warn("jQuery.fn.enableAccessKeys: node type [" + $form.attr("nodeName") + "] is not valid. " + "Only <form> supported");
            return this
        }
        if ($form.data("handleAccessKeys.applied")) {
            return
        }
        $form.data("handleAccessKeys.applied", true);
        $form.find(":input[" + accessKeyAttr + "], a[" + accessKeyAttr + "]").each(function () {
            var accessKey = jQuery(this).attr(accessKeyAttr);
            if (accessKey) {
                blackList.push(accessKey.toLowerCase())
            }
        });
        $form.delegate(":input, a", "focus", function () {
            removeAccessKeys($accessKeyElems, blackList);
            attachAccessKeys($myAccessKeyElems)
        }).delegate(":input, a", "blur", function () {
            attachAccessKeys($accessKeyElems)
        })
    });

    function isInvalid(key, blackList) {
        if (key) {
            if (options.selective === false) {
                return true
            }
            if (/[a-z]/i.test(key)) {
                key = key.toLowerCase()
            }
            return jQuery.inArray(key, blackList) !== -1
        }
    }
    function attachAccessKeys($accessKeyElems) {
        $accessKeyElems.each(function () {
            var $this = AJS.$(this);
            if ($this.data(accessKeyAttr)) {
                $this.attr(accessKeyAttr, $this.data(accessKeyAttr))
            }
        })
    }
    function removeAccessKeys($accessKeyElems, blackList) {
        $accessKeyElems.each(function () {
            var $this = AJS.$(this);
            if (isInvalid($this.attr(accessKeyAttr), blackList)) {
                $this.data(accessKeyAttr, $this.attr(accessKeyAttr));
                $this.removeAttr(accessKeyAttr)
            }
        })
    }
    return this
};
new function (settings) {
    var $separator = settings.separator || "&";
    var $spaces = settings.spaces === false ? false : true;
    var $suffix = settings.suffix === false ? "" : "[]";
    var $prefix = settings.prefix === false ? false : true;
    var $hash = $prefix ? settings.hash === true ? "#" : "?" : "";
    var $numbers = settings.numbers === false ? false : true;
    jQuery.query = new function () {
        var is = function (o, t) {
                return o != undefined && o !== null && ( !! t ? o.constructor == t : true)
            };
        var parse = function (path) {
                var m, rx = /\[([^[]*)\]/g,
                    match = /^([^[]+)(\[.*\])?$/.exec(path),
                    base = match[1],
                    tokens = [];
                while (m = rx.exec(match[2])) {
                    tokens.push(m[1])
                }
                return [base, tokens]
            };
        var set = function (target, tokens, value) {
                var o, token = tokens.shift();
                if (typeof target != "object") {
                    target = null
                }
                if (token === "") {
                    if (!target) {
                        target = []
                    }
                    if (is(target, Array)) {
                        target.push(tokens.length == 0 ? value : set(null, tokens.slice(0), value))
                    } else {
                        if (is(target, Object)) {
                            var i = 0;
                            while (target[i++] != null) {}
                            target[--i] = tokens.length == 0 ? value : set(target[i], tokens.slice(0), value)
                        } else {
                            target = [];
                            target.push(tokens.length == 0 ? value : set(null, tokens.slice(0), value))
                        }
                    }
                } else {
                    if (token && token.match(/^\s*[0-9]+\s*$/)) {
                        var index = parseInt(token, 10);
                        if (!target) {
                            target = []
                        }
                        target[index] = tokens.length == 0 ? value : set(target[index], tokens.slice(0), value)
                    } else {
                        if (token) {
                            var index = token.replace(/^\s*|\s*$/g, "");
                            if (!target) {
                                target = {}
                            }
                            if (is(target, Array)) {
                                var temp = {};
                                for (var i = 0; i < target.length; ++i) {
                                    temp[i] = target[i]
                                }
                                target = temp
                            }
                            target[index] = tokens.length == 0 ? value : set(target[index], tokens.slice(0), value)
                        } else {
                            return value
                        }
                    }
                }
                return target
            };
        var queryObject = function (a) {
                var self = this;
                self.keys = {};
                if (a.queryObject) {
                    jQuery.each(a.get(), function (key, val) {
                        self.SET(key, val)
                    })
                } else {
                    jQuery.each(arguments, function () {
                        var q = "" + this;
                        q = q.replace(/^[?#]/, "");
                        q = q.replace(/[;&]$/, "");
                        if ($spaces) {
                            q = q.replace(/[+]/g, " ")
                        }
                        jQuery.each(q.split(/[&;]/), function () {
                            var key = decodeURIComponent(this.split("=")[0] || "");
                            var val = decodeURIComponent(this.split("=")[1] || "");
                            if (!key) {
                                return
                            }
                            if ($numbers) {
                                if (/^[+-]?[0-9]+\.[0-9]*$/.test(val)) {
                                    val = parseFloat(val)
                                } else {
                                    if (/^[+-]?[0-9]+$/.test(val)) {
                                        val = parseInt(val, 10)
                                    }
                                }
                            }
                            val = (!val && val !== 0) ? true : val;
                            if (val !== false && val !== true && typeof val != "number") {
                                val = val
                            }
                            self.SET(key, val)
                        })
                    })
                }
                return self
            };
        queryObject.prototype = {
            queryObject: true,
            has: function (key, type) {
                var value = this.get(key);
                return is(value, type)
            },
            GET: function (key) {
                if (!is(key)) {
                    return this.keys
                }
                var parsed = parse(key),
                    base = parsed[0],
                    tokens = parsed[1];
                var target = this.keys[base];
                while (target != null && tokens.length != 0) {
                    target = target[tokens.shift()]
                }
                return typeof target == "number" ? target : target || ""
            },
            get: function (key) {
                var target = this.GET(key);
                if (is(target, Object)) {
                    return jQuery.extend(true, {}, target)
                } else {
                    if (is(target, Array)) {
                        return target.slice(0)
                    }
                }
                return target
            },
            SET: function (key, val) {
                var value = !is(val) ? null : val;
                var parsed = parse(key),
                    base = parsed[0],
                    tokens = parsed[1];
                var target = this.keys[base];
                this.keys[base] = set(target, tokens.slice(0), value);
                return this
            },
            set: function (key, val) {
                return this.copy().SET(key, val)
            },
            REMOVE: function (key) {
                return this.SET(key, null).COMPACT()
            },
            remove: function (key) {
                return this.copy().REMOVE(key)
            },
            EMPTY: function () {
                var self = this;
                jQuery.each(self.keys, function (key, value) {
                    delete self.keys[key]
                });
                return self
            },
            load: function (url) {
                var hash = url.replace(/^.*?[#](.+?)(?:\?.+)?$/, "$1");
                var search = url.replace(/^.*?[?](.+?)(?:#.+)?$/, "$1");
                return new queryObject(url.length == search.length ? "" : search, url.length == hash.length ? "" : hash)
            },
            empty: function () {
                return this.copy().EMPTY()
            },
            copy: function () {
                return new queryObject(this)
            },
            COMPACT: function () {
                function build(orig) {
                    var obj = typeof orig == "object" ? is(orig, Array) ? [] : {} : orig;
                    if (typeof orig == "object") {
                        function add(o, key, value) {
                            if (is(o, Array)) {
                                o.push(value)
                            } else {
                                o[key] = value
                            }
                        }
                        jQuery.each(orig, function (key, value) {
                            if (!is(value)) {
                                return true
                            }
                            add(obj, key, build(value))
                        })
                    }
                    return obj
                }
                this.keys = build(this.keys);
                return this
            },
            compact: function () {
                return this.copy().COMPACT()
            },
            toString: function () {
                var i = 0,
                    queryString = [],
                    chunks = [],
                    self = this;
                var encode = function (str) {
                        str = str + "";
                        if ($spaces) {
                            str = str.replace(/ /g, "+")
                        }
                        return encodeURIComponent(str)
                    };
                var addFields = function (arr, key, value) {
                        if (!is(value) || value === false) {
                            return
                        }
                        var o = [encode(key)];
                        if (value !== true) {
                            o.push("=");
                            o.push(encode(value))
                        }
                        arr.push(o.join(""))
                    };
                var build = function (obj, base) {
                        var newKey = function (key) {
                                return !base || base == "" ? [key].join("") : [base, "[", key, "]"].join("")
                            };
                        jQuery.each(obj, function (key, value) {
                            if (typeof value == "object") {
                                build(value, newKey(key))
                            } else {
                                addFields(chunks, newKey(key), value)
                            }
                        })
                    };
                build(this.keys);
                if (chunks.length > 0) {
                    queryString.push($hash)
                }
                queryString.push(chunks.join($separator));
                return queryString.join("")
            }
        };
        return new queryObject(location.search, location.hash)
    }
}(jQuery.query || {});
jQuery.fn.preventBlurFromElements = function () {
    var elems = jQuery.makeArray(arguments),
        $fromElement = this;
    if (jQuery.browser.msie) {
        $fromElement.bind("beforedeactivate", function (e) {
            var blurEvents;
            if (e.toElement === document.documentElement) {
                blurEvents = $fromElement.data("events").blur;
                delete $fromElement.data("events").blur;
                $fromElement.one("blur", function () {
                    $fromElement.focus();
                    window.setTimeout(function () {
                        $fromElement.data("events").blur = blurEvents
                    }, 0)
                })
            } else {
                jQuery.each(elems, function () {
                    var $elem = jQuery(this);
                    if ($elem.has(e.toElement).length === 1) {
                        e.preventDefault();
                        return false
                    }
                })
            }
        })
    } else {
        jQuery.each(elems, function () {
            this.mousedown(function (e) {
                if (e.target !== $fromElement[0]) {
                    e.preventDefault()
                }
            })
        })
    }
};
jQuery.fn.setSelectionRange = function (selectionStart, selectionEnd) {
    if (this.length == 0) {
        return
    }
    if (this[0].setSelectionRange) {
        this[0].focus();
        this[0].setSelectionRange(selectionStart, selectionEnd)
    } else {
        if (this[0].createTextRange) {
            var range = this[0].createTextRange();
            range.collapse(true);
            range.moveEnd("character", selectionEnd);
            range.moveStart("character", selectionStart);
            range.select()
        }
    }
}, jQuery.fn.setCaretToPosition = function (position) {
    this.setSelectionRange(position, position)
};
JIRA.SessionStorage = {};
(function () {
    var MAGIC_MARK = "jsessionstorage:";
    var nonNativeSessionStorageObjInitialised = false;
    var nonNativeSessionStorageObj = {};
    var nonNativeSessionStorageImpl = {
        nonnativeimplementation: true,
        _storage: function () {
            if (nonNativeSessionStorageObjInitialised) {
                return nonNativeSessionStorageObj
            }
            if (typeof window.name != "string") {
                window.name = MAGIC_MARK + "{}"
            }
            if (window.name.indexOf(MAGIC_MARK) != 0) {
                window.name = MAGIC_MARK + "{}"
            }
            var jsonData = window.name.substr(MAGIC_MARK.length);
            nonNativeSessionStorageObj = JSON.parse(jsonData);
            if (!nonNativeSessionStorageObj) {
                nonNativeSessionStorageObj = {}
            }
            nonNativeSessionStorageObjInitialised = true;
            return nonNativeSessionStorageObj
        },
        _persistStorage: function () {
            var storeObj = this._storage();
            var jsonData = JSON.stringify(storeObj);
            window.name = MAGIC_MARK + jsonData
        },
        length: function () {
            var i = 0;
            var store = this._storage();
            for (var x in store) {
                i++
            }
            return i
        },
        key: function (index) {
            var i = 0;
            var store = this._storage();
            for (var x in store) {
                if (i == index) {
                    return x
                }
                i++
            }
            return null
        },
        getItem: function (key) {
            return this._storage()[key]
        },
        setItem: function (key, value) {
            this._storage()[key] = value;
            this._persistStorage()
        },
        removeItem: function (key) {
            delete this._storage()[key];
            this._persistStorage()
        },
        clear: function () {
            var store = this._storage();
            for (var x in store) {
                delete x
            }
            this._persistStorage()
        }
    };
    var _sessionStorageImpl = window.sessionStorage != null ? window.sessionStorage : nonNativeSessionStorageImpl;
    JIRA.SessionStorage.nativesupport = window.sessionStorage != null;
    JIRA.SessionStorage.length = function () {
        if (typeof _sessionStorageImpl.length == "function") {
            return _sessionStorageImpl.length()
        }
        return _sessionStorageImpl.length
    };
    JIRA.SessionStorage.key = function (index) {
        return _sessionStorageImpl.key(index)
    };
    JIRA.SessionStorage.getItem = function (key) {
        return _sessionStorageImpl.getItem(key)
    };
    JIRA.SessionStorage.setItem = function (key, value) {
        _sessionStorageImpl.setItem(key, value)
    };
    JIRA.SessionStorage.removeItem = function (key) {
        _sessionStorageImpl.removeItem(key)
    };
    JIRA.SessionStorage.clear = function () {
        _sessionStorageImpl.clear()
    };
    JIRA.SessionStorage.asJSON = function () {
        var len = this.length();
        var jsData = "{\n";
        for (var i = 0; i < len; i++) {
            var key = this.key(i);
            var value = this.getItem(key);
            jsData += key + ":" + value;
            if (i < len - 1) {
                jsData += ","
            }
            jsData += "\n"
        }
        jsData += "}\n";
        return jsData
    }
})(jQuery);
AJS.namespace("jira.app.session.storage", null, JIRA.SessionStorage);
AJS.nextPage = function () {
    var data = [],
        oldBeforeUnload = window.onbeforeunload;
    window.onbeforeunload = function () {
        if (window.sessionStorage) {
            sessionStorage.setItem("AJS.thisPage", JSON.stringify(data))
        } else {
            saveCookie("AJS.thisPage", JSON.stringify(data))
        }
        if (oldBeforeUnload) {
            oldBeforeUnload()
        }
    };
    return function (name, value) {
        var replaced;
        jQuery.each(data, function () {
            if (this.name === name) {
                this.value = value;
                replaced = true
            }
        });
        if (!replaced) {
            data.push({
                name: name,
                value: value
            })
        }
    }
}();
AJS.thisPage = function () {
    var i, value, unformattedData, data = {};
    if (window.sessionStorage) {
        unformattedData = sessionStorage.getItem("AJS.thisPage");
        sessionStorage.removeItem("AJS.thisPage")
    } else {
        unformattedData = readCookie("AJS.thisPage");
        eraseCookie("AJS.thisPage")
    }
    if (unformattedData) {
        unformattedData = JSON.parse(unformattedData);
        for (i = 0; i < unformattedData.length; i++) {
            data[unformattedData[i].name] = unformattedData[i].value
        }
    }
    return function (key) {
        return data[key]
    }
}();
(function () {
    var META_TOKEN_ID = "atlassian-token";
    var PARAM_TOKEN_NAME = "atl_token";
    var INPUT_TOKEN_NAME = "atl_token";
    var tokenQueryParam = function (token) {
            return AJS.format("{0}={1}", PARAM_TOKEN_NAME, token)
        };
    var replaceTokenInMeta = function (oldToken, newToken) {
            var metaTokenSelector = AJS.format("meta#{0}", META_TOKEN_ID);
            AJS.$(metaTokenSelector).attr("content", newToken)
        };
    var replaceTokenInLinks = function (oldToken, newToken) {
            AJS.$("a,link").each(function () {
                var link = AJS.$(this);
                var href = link.attr("href");
                if (href) {
                    link.attr("href", href.replace(tokenQueryParam(oldToken), tokenQueryParam(newToken)))
                }
            })
        };
    var replaceTokenInForms = function (oldToken, newToken) {
            AJS.$("form").each(function () {
                var $form = AJS.$(this);
                var action = $form.attr("action");
                if (action) {
                    $form.attr("DAGGER_ACTION", action.replace(tokenQueryParam(oldToken), tokenQueryParam(newToken)))
                }
                var formInputSelector = AJS.format("input[name={0}][value={1}]", INPUT_TOKEN_NAME, oldToken);
                $form.find(formInputSelector).each(function () {
                    AJS.$(this).attr("value", newToken)
                })
            })
        };
    AJS.namespace("JIRA.XSRF");
    if (typeof JIRA.XSRF.updateTokenOnPage !== "function") {
        JIRA.XSRF.updateTokenOnPage = function (newToken) {
            var oldToken = atl_token();
            if (oldToken !== newToken) {
                replaceTokenInMeta(oldToken, newToken);
                replaceTokenInLinks(oldToken, newToken);
                replaceTokenInForms(oldToken, newToken)
            }
        }
    }
}());
AJS.namespace("jira.xsrf", null, JIRA.XSRF);
AJS.ContentRetriever = Class.extend({
    startingRequest: jQuery.noop,
    finishedRequest: jQuery.noop,
    cache: jQuery.noop,
    isLocked: jQuery.noop,
    content: jQuery.noop
});
AJS.AjaxContentRetriever = AJS.ContentRetriever.extend({
    init: function (options) {
        var instance = this;
        this.ajaxOptions = options;
        if (typeof this.ajaxOptions === "string") {
            this.ajaxOptions = {
                url: this.ajaxOptions
            }
        }
        this.ajaxOptions.globalThrobber = false;
        this.ajaxOptions.success = function (data, textStatus, xhr) {
            instance._requestComplete(xhr, textStatus, data, true, null)
        };
        this.ajaxOptions.error = function (xhr, textStatus) {
            if (xhr.rc) {
                xhr.status = xhr.rc
            }
            instance._requestComplete(xhr, textStatus, null, false, null)
        }
    },
    content: function (arg) {
        if (AJS.$.isFunction(arg)) {
            this.callback = arg;
            this._makeRequest(arg)
        } else {
            if (arg) {
                this.callback(arg);
                delete this.callback
            }
        }
        return this.$content
    },
    startingRequest: function (callback) {
        if (callback) {
            this.startingCallback = callback
        } else {
            if (this.startingCallback) {
                this.locked = true;
                this.startingCallback()
            }
        }
    },
    finishedRequest: function (callback) {
        if (callback) {
            this.finishedCallback = callback
        } else {
            if (this.finishedCallback) {
                this.locked = false;
                this.finishedCallback()
            }
        }
    },
    cache: function (cache) {
        if (typeof cache !== "undefined") {
            this.ajaxOptions.cache = cache
        }
        return this.ajaxOptions.cache
    },
    isLocked: function () {
        return this.locked
    },
    _requestComplete: function (xhr, statusText, data, successful, errorThrown) {
        var $content, smartAjaxResult;
        if (JIRA.SmartAjax.SmartAjaxResult) {
            smartAjaxResult = JIRA.SmartAjax.SmartAjaxResult.apply(window, arguments)
        }
        if (successful) {
            if (AJS.$.isFunction(this.ajaxOptions.formatSuccess)) {
                $content = this.ajaxOptions.formatSuccess(data)
            } else {
                $content = AJS.$("<div>" + data + "</div>")
            }
        } else {
            if (AJS.$.isFunction(this.ajaxOptions.formatError)) {
                $content = this.ajaxOptions.formatError(data)
            } else {
                if (smartAjaxResult) {
                    var errorClass = smartAjaxResult.status === 401 ? "warn" : "error";
                    $content = AJS.$("<div class='notify " + errorClass + "'>" + JIRA.SmartAjax.buildSimpleErrorContent(smartAjaxResult) + "</div>")
                }
            }
        }
        this.content($content);
        this.finishedRequest()
    },
    _makeRequest: function () {
        this.startingRequest();
        AJS.$.ajax(this.ajaxOptions)
    }
});
AJS.DOMContentRetriever = AJS.ContentRetriever.extend({
    init: function (content) {
        this.$content = AJS.$(content)
    },
    content: function (callback) {
        if (AJS.$.isFunction(callback)) {
            callback(this.$content)
        }
        return this.$content
    },
    cache: function () {},
    isLocked: function () {},
    startingRequest: function () {},
    finishedRequest: function () {}
});
AJS.InlineLayer = AJS.Control.extend({
    CLASS_SIGNATURE: "AJS_InlineLayer",
    SCROLL_HIDE_EVENT: "scroll.hide-dropdown",
    init: function (options) {
        var instance = this;
        if (!(options instanceof AJS.InlineLayer.OptionsDescriptor)) {
            this.options = new AJS.InlineLayer.OptionsDescriptor(options)
        } else {
            this.options = options
        }
        this.offsetTarget(this.options.offsetTarget());
        this.contentRetriever = this.options.contentRetriever();
        this.positionController = this.options.positioningController();
        if (!(this.contentRetriever instanceof AJS.ContentRetriever)) {
            throw new Error("AJS.InlineLayer: Failed construction, Content retriever does not implement interface " + "[AJS.ContentRetrieverInterface]")
        }
        this.contentRetriever.startingRequest(function () {
            instance._showLoading()
        });
        this.contentRetriever.finishedRequest(function () {
            instance._hideLoading()
        });
        this.$layer = this._render("layer", this.options.alignment())
    },
    content: function (arg) {
        var instance = this;
        if (AJS.$.isFunction(arg)) {
            if (this.contentRetriever.isLocked()) {
                throw new Error(this.CLASS_SIGNATURE + ".content() : Illegal operation, trying to access content while it is " + "locked. If you are seeing this error it is most likely because we are waiting for an request to " + "come back from the server that builds content")
            }
            this.contentRetriever.content(function (content) {
                instance.$content = content.removeClass("hidden");
                arg.call(instance)
            })
        } else {
            return this.$content
        }
    },
    offsetTarget: function (offsetTarget) {
        if (offsetTarget) {
            this.$offsetTarget = AJS.$(offsetTarget)
        }
        return this.$offsetTarget
    },
    contentChange: function (callback) {
        var event, instance = this;
        if (AJS.$.isFunction(callback)) {
            if (!this.contentChangeCallback) {
                this.contentChangeCallback = []
            }
            this.contentChangeCallback.push(callback)
        } else {
            if (!callback && this.contentChangeCallback) {
                AJS.$.each(this.contentChangeCallback, function (i, callback) {
                    callback(instance)
                });
                AJS.trigger("contentChange", this.layer());
                this.setWidth(this.options.width())
            }
        }
    },
    onhide: function (callback) {
        var instance = this;
        if (AJS.$.isFunction(callback)) {
            if (!this.hideCallback) {
                this.hideCallback = []
            }
            this.hideCallback.push(callback)
        } else {
            if (!callback && this.hideCallback) {
                AJS.$.each(this.hideCallback, function (i, callback) {
                    callback(instance)
                })
            }
        }
    },
    layer: function (layer) {
        if (layer) {
            this.$layer = layer
        } else {
            return this.$layer
        }
    },
    placeholder: function (placeholder) {
        if (placeholder) {
            this._throwReadOnlyError("placeholder")
        } else {
            return this.$placeholder
        }
    },
    isVisible: function (visible) {
        if (typeof visible !== "undefined") {
            this._throwReadOnlyError("visible")
        }
        return this.visible
    },
    scrollableContainer: function (scrollableContainer) {
        if (scrollableContainer) {
            this._throwReadOnlyError("scrollableContainer")
        }
        return this.$scrollableContainer
    },
    isInitialized: function (initialised) {
        if (initialised) {
            this._throwReadOnlyError("initialized")
        }
        return this.initialized
    },
    hide: function () {
        if (!this.isVisible()) {
            return false
        }
        this.visible = false;
        this.layer().removeClass(AJS.ACTIVE_CLASS).hide();
        this.$offsetTarget.removeClass(AJS.ACTIVE_CLASS);
        var positionController = this.positionController;
        setTimeout(function () {
            positionController.appendToPlaceholder()
        }, 0);
        this._unbindEvents();
        this.onhide();
        AJS.$(document).trigger("hideLayer", [this.CLASS_SIGNATURE, this]);
        AJS.InlineLayer.current = null
    },
    refreshContent: function (callback) {
        var instance = this;
        this.content(function () {
            this.layer().empty().append(this.content());
            if (AJS.$.isFunction(callback)) {
                callback.call(instance)
            }
            this.contentChange()
        })
    },
    show: function () {
        var instance = this;
        if (this.isVisible()) {
            return
        }
        if (!this.isInitialized()) {
            this._lazyInit(function () {
                instance._show()
            })
        } else {
            if (this.contentRetriever.cache() === false) {
                this.refreshContent(function () {
                    instance._show()
                })
            } else {
                instance._show()
            }
        }
    },
    setPosition: function () {
        var positioning, scrollTop;
        if (!this.isInitialized() || !this.offsetTarget() || this.offsetTarget().length === 0) {
            return
        }
        if (this.options.alignment() === AJS.RIGHT) {
            positioning = this.positionController.right()
        } else {
            if (this.options.alignment() === AJS.LEFT) {
                positioning = this.positionController.left()
            } else {
                if (this.options.alignment() === AJS.INTELLIGENT_GUESS) {
                    if ((this.offsetTarget().offset().left + this.layer().outerWidth() / 2) > AJS.$(window).width() / 2) {
                        positioning = this.positionController.right()
                    } else {
                        positioning = this.positionController.left()
                    }
                }
            }
        }
        if (AJS.dim.dim) {
            scrollTop = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
            positioning.maxHeight = AJS.$(window).height() + scrollTop - positioning.top - this.options.cushion()
        }
        this.layer().css(positioning)
    },
    setWidth: function (width, showhorizontalScroll) {
        var contentScrollWidth = this.content().attr("scrollWidth");
        if (!(this.content().hasClass("error") || this.content().hasClass("warn"))) {
            this.content().css({
                whiteSpace: "nowrap",
                overflowX: "",
                width: "auto"
            })
        }
        if (contentScrollWidth <= width) {
            this.layer().css({
                width: width,
                whiteSpace: ""
            })
        } else {
            if (showhorizontalScroll) {
                this.layer().css({
                    width: width,
                    overflowX: "auto",
                    whiteSpace: ""
                })
            } else {
                this.layer().css({
                    width: contentScrollWidth,
                    overflowX: "hidden",
                    whiteSpace: ""
                })
            }
        }
    },
    _lazyInit: function (callback) {
        this.initialized = true;
        this.refreshContent(function () {
            var instance = this;
            this.layer().insertAfter(this.offsetTarget());
            if (this._supportsBoxShadow()) {
                this.layer().addClass(AJS.BOX_SHADOW_CLASS)
            }
            this.$placeholder = AJS.$("<div class='ajs-layer-placeholder' />").insertAfter(this.offsetTarget());
            this.$scrollableContainer = this.offsetTarget().closest(this.options.hideOnScroll());
            this.positionController.set(this.layer(), this.offsetTarget(), this.placeholder());
            this.positionController.rebuilt(function (layer) {
                instance.layer(layer)
            });
            callback()
        })
    },
    _show: function () {
        if (AJS.InlineLayer.current) {
            AJS.InlineLayer.current.hide()
        }
        this.visible = true;
        this.content().show();
        this.positionController.appendToBody();
        this.layer().addClass(AJS.ACTIVE_CLASS);
        this.$offsetTarget.addClass(AJS.ACTIVE_CLASS);
        this.layer().show();
        this.setWidth(this.options.width());
        this.setPosition();
        this._bindEvents();
        if (!AJS.dim.dim) {
            this.positionController.scrollTo()
        }
        AJS.InlineLayer.current = this;
        AJS.$(document).trigger("showLayer", [this.CLASS_SIGNATURE, this])
    },
    _hideLoading: function () {
        this.$layer.removeClass(AJS.LOADING_CLASS);
        this.$offsetTarget.removeClass(AJS.LOADING_CLASS)
    },
    _showLoading: function () {
        this.$layer.addClass(AJS.LOADING_CLASS);
        this.$offsetTarget.addClass(AJS.LOADING_CLASS)
    },
    _unbindEvents: function () {
        this.$scrollableContainer.unbind(this.SCROLL_HIDE_EVENT);
        this._unassignEvents("container", document);
        this._unassignEvents("win", window)
    },
    _bindEvents: function () {
        var instance = this;
        this.$scrollableContainer.one(this.SCROLL_HIDE_EVENT, function () {
            instance.hide()
        });
        this._assignEvents("container", document);
        this._assignEvents("win", window)
    },
    _validateClickToClose: function (e) {
        if (e.target === this.offsetTarget()[0]) {
            return false
        } else {
            if (e.target === this.layer()[0]) {
                return false
            } else {
                if (this.offsetTarget().has(e.target).length > 0) {
                    return false
                } else {
                    if (this.layer().has(e.target).length > 0) {
                        return false
                    }
                }
            }
        }
        return true
    },
    _events: {
        container: {
            keydown: function (e) {
                if (JIRA.Keyboard.specialKeyEntered(e) === JIRA.Keyboard.SpecialKey.ESC) {
                    this.hide()
                }
            },
            keypress: function (e) {
                if (JIRA.Keyboard.specialKeyEntered(e) === JIRA.Keyboard.SpecialKey.ESC) {
                    this.hide()
                }
            },
            click: function (e) {
                if (this._validateClickToClose(e)) {
                    this.hide()
                }
            }
        },
        win: {
            resize: function () {
                this.setPosition()
            },
            scroll: function () {
                this.setPosition()
            }
        }
    },
    _renders: {
        layer: function () {
            return AJS.$("<div />").addClass("ajs-layer " + (this.options.styleClass() || ""))
        }
    }
});
AJS.InlineLayer.OptionsDescriptor = AJS.Descriptor.extend({
    init: function (properties) {
        this._super(properties);
        if (!this.contentRetriever()) {
            if (this.ajaxOptions()) {
                this.contentRetriever(new AJS.AjaxContentRetriever(this.ajaxOptions()))
            } else {
                if (this.content()) {
                    this.contentRetriever(new AJS.DOMContentRetriever(this.content()))
                } else {
                    throw new Error("AJS.InlineLayer.OptionsDescriptor: Expected either [ajaxOptions] or [contentRetriever] or [content] to be defined")
                }
            }
        }
        if (!AJS.params.ignoreFrame && this._inIFrame()) {
            this.positioningController(new AJS.InlineLayer.IframePositioning())
        } else {
            this.positioningController(new AJS.InlineLayer.StandardPositioning())
        }
        if (AJS.$.browser.msie && this.positioningController().isOffsetIncludingScroll) {
            this.positioningController().isOffsetIncludingScroll(false)
        }
        if (!this.offsetTarget() && this.content()) {
            this.offsetTarget(this.content().prev())
        }
    },
    _inIFrame: function () {
        var parentWindow = window;
        try {
            while (parentWindow.parent.window !== parentWindow.window) {
                parentWindow = parentWindow.parent;
                if (parentWindow.AJS) {
                    return true
                }
            }
        } catch (error) {}
        return false
    },
    _getDefaultOptions: function () {
        return {
            alignment: AJS.INTELLIGENT_GUESS,
            hideOnScroll: ".content-body",
            cushion: 20,
            width: 200
        }
    },
    positioningController: function (positioningController) {
        if (positioningController) {
            this.properties.positioningController = positioningController
        } else {
            return this.properties.positioningController
        }
    },
    ajaxOptions: function (ajaxOptions) {
        if (ajaxOptions) {
            this.properties.ajaxOptions = ajaxOptions
        } else {
            return this.properties.ajaxOptions
        }
    },
    content: function (content) {
        if (content) {
            content = AJS.$(content);
            if (content.length) {
                this.properties.content = content
            }
        } else {
            if (this.properties.content && this.properties.content.length) {
                return this.properties.content
            }
        }
    },
    contentRetriever: function (contentRetriever) {
        if (contentRetriever) {
            this.properties.contentRetriever = contentRetriever
        } else {
            return this.properties.contentRetriever
        }
    },
    offsetTarget: function (offsetTarget) {
        if (offsetTarget) {
            offsetTarget = AJS.$(offsetTarget);
            if (offsetTarget.length) {
                this.properties.offsetTarget = offsetTarget
            }
        } else {
            if (this.properties.offsetTarget && this.properties.offsetTarget.length) {
                return this.properties.offsetTarget
            }
        }
    },
    cushion: function (cushion) {
        if (cushion) {
            this.properties.cushion = cushion
        } else {
            return this.properties.cushion
        }
    },
    styleClass: function (styleClass) {
        if (styleClass) {
            this.properties.styleClass = styleClass
        } else {
            return this.properties.styleClass
        }
    },
    hideOnScroll: function (hideOnScroll) {
        if (hideOnScroll) {
            this.properties.hideOnScroll = hideOnScroll
        } else {
            return this.properties.hideOnScroll
        }
    },
    alignment: function (alignment) {
        if (alignment) {
            this.properties.alignment = alignment
        } else {
            return this.properties.alignment
        }
    },
    width: function (width) {
        if (width) {
            this.properties.width = width
        } else {
            return this.properties.width
        }
    }
});
AJS.InlineLayer.create = function (options) {
    var inlineLayers = [];
    if (options.content) {
        options.content = AJS.$(options.content);
        AJS.$.each(options.content, function () {
            var instanceOptions = AJS.copyObject(options);
            instanceOptions.content = AJS.$(this);
            inlineLayers.push(new AJS.InlineLayer(instanceOptions))
        })
    }
    if (inlineLayers.length == 1) {
        return inlineLayers[0]
    } else {
        return inlineLayers
    }
};
AJS.InlineLayer.StandardPositioning = Class.extend({
    set: function ($layer, $offsetTarget, $placeholder) {
        this.$layer = $layer;
        this.$offsetTarget = $offsetTarget;
        this.$placeholder = $placeholder;
        this.rebuiltCallbacks = []
    },
    left: function () {
        var offset = this.offset();
        return {
            left: offset.left,
            top: offset.top + this.$offsetTarget.outerHeight()
        }
    },
    right: function () {
        var offset = this.offset();
        return {
            left: this.offset().left - this.$layer.outerWidth() + this.$offsetTarget.outerWidth(),
            top: offset.top + this.$offsetTarget.outerHeight()
        }
    },
    window: function () {
        return window
    },
    offset: function () {
        var offset = this.$offsetTarget.offset();
        if (this.$offsetTarget.hasFixedParent()) {
            this.$layer.css("position", "fixed");
            offset.top = offset.top - AJS.$(window).scrollTop()
        } else {
            this.$layer.css("position", "absolute")
        }
        return offset
    },
    rebuilt: function (arg) {
        var instance = this;
        if (AJS.$.isFunction(arg)) {
            this.rebuiltCallbacks.push(arg)
        } else {
            AJS.$.each(this.rebuiltCallbacks, function () {
                this(instance.$layer)
            })
        }
    },
    appendToBody: function () {
        this.$layer.appendTo("body")
    },
    appendToPlaceholder: function () {
        this.$layer.appendTo(this.$placeholder)
    },
    scrollTo: function () {}
});
AJS.InlineLayer.IframePositioning = AJS.InlineLayer.StandardPositioning.extend({
    offset: function () {
        var offsetInDocument = this._super(),
            iframeOffset = AJS.$("iframe[name=" + window.name + "]", window.top.document.body).parent().offset(),
            topDocumentScrollTop = this._topDocumentScrollTop(),
            topDocumentScrollLeft = this._topDocumentScrollLeft(),
            iframeScrollTop = this._iframeScrollTop(),
            iframeScrollLeft = this._iframeScrollLeft(),
            scrollTopModifier = topDocumentScrollTop - iframeScrollTop,
            scrollLeftModifier = topDocumentScrollLeft - iframeScrollLeft;
        return {
            left: iframeOffset.left + offsetInDocument.left + scrollLeftModifier,
            top: iframeOffset.top + offsetInDocument.top + scrollTopModifier
        }
    },
    _topDocumentScrollTop: function () {
        return this.isOffsetIncludingScroll() ? 0 : Math.max(window.top.document.body.scrollTop, window.top.document.documentElement.scrollTop)
    },
    _topDocumentScrollLeft: function () {
        return this.isOffsetIncludingScroll() ? 0 : Math.max(window.top.document.body.scrollLeft, window.top.document.documentElement.scrollLeft)
    },
    _iframeScrollTop: function () {
        return this.isOffsetIncludingScroll() ? 2 * Math.max(window.document.body.scrollTop, window.document.documentElement.scrollTop) : 0
    },
    _iframeScrollLeft: function () {
        return this.isOffsetIncludingScroll() ? 2 * Math.max(window.document.body.scrollLeft, window.document.documentElement.scrollLeft) : 0
    },
    isOffsetIncludingScroll: function (offsetIncludingScroll) {
        if (typeof this.offsetIncludingScroll === "undefined") {
            this.offsetIncludingScroll = true
        }
        if (typeof offsetIncludingScroll !== "undefined") {
            this.offsetIncludingScroll = offsetIncludingScroll
        }
        return this.offsetIncludingScroll
    },
    appendToBody: function () {
        window.top.jQuery("body").append(this.$layer)
    },
    window: function () {
        return window.top
    },
    scrollTo: function () {}
});
if (AJS.$.browser.safari || (AJS.$.browser.msie && AJS.$.browser.version < 8)) {
    AJS.InlineLayer.IframePositioning = AJS.InlineLayer.IframePositioning.extend({
        appendToBody: function () {
            this.$layer = this._rebuildLayerInParent();
            window.top.jQuery("body").append(this.$layer);
            this.rebuilt()
        },
        appendToPlaceholder: function () {
            this.$layer = this._rebuildLayerInIframe();
            this.$layer.appendTo(this.$placeholder);
            this.rebuilt()
        },
        _rebuildLayerInParent: function () {
            return window.top.jQuery("<div class='ajs-layer'>" + this.$layer.html() + "</div>")
        },
        _rebuildLayerInIframe: function () {
            return AJS.$("<div class='ajs-layer'>" + this.$layer.html() + "</div>")
        }
    })
};
AJS.Group = AJS.Control.extend({
    init: function () {
        this.items = [];
        this.index = -1;
        this._assignEvents("instance", this)
    },
    addItem: function (item) {
        this.items.push(item);
        this._assignEvents("item", item)
    },
    removeItem: function (item) {
        var index = AJS.$.inArray(item, this.items);
        if (index < 0) {
            throw new Error("AJS.Group: item [" + item + "] is not a member of this group")
        }
        item.trigger("blur");
        if (index < this.index) {
            this.index--
        }
        this.items.splice(index, 1);
        this._unassignEvents("item", item)
    },
    removeAllItems: function () {
        for (var i = 0; i < this.items.length; i++) {
            this._unassignEvents("item", this.items[i]);
	    this.items[i].trigger("remove");
            this.items[i].trigger("blur")
        }
        this.index = -1;
        this.items.length = 0;
        this._unassignEvents("keys", document)
    },
    shiftFocus: function (offset) {
        if (this.index === -1 && offset === 1) {
            offset = 0
        }
        if (this.items.length > 0) {
            var i = (Math.max(0, this.index) + this.items.length + offset) % this.items.length;
            this.items[i].trigger("focus")
        }
    },
    prepareForInput: function () {
        this._assignEvents("keys", document)
    },
    _events: {
        "instance": {
            "focus": function () {
                if (this.items.length === 0) {
                    return
                }
                if (this.index < 0) {
                    this.items[0].trigger("focus")
                } else {
                    this._assignEvents("keys", document)
                }
            },
            "blur": function () {
                if (this.index >= 0) {
                    this.items[this.index].trigger("blur")
                } else {
                    this._unassignEvents("keys", document)
                }
            }
        },
        "keys": {
            "keydown keypress": function (event) {
                this._handleKeyEvent(event)
            }
        },
        "item": {
            "focus": function (event) {
                var index = this.index;
                this.index = AJS.$.inArray(event.target, this.items);
                if (index < 0) {
                    this.trigger("focus")
                } else {
                    if (index !== this.index) {
                        this.items[index].trigger("blur")
                    }
                }
            },
            "blur": function (event) {
                if (this.index === AJS.$.inArray(event.target, this.items)) {
                    this.index = -1;
                    this.trigger("blur")
                }
            },
            "remove": function (event) {
                this.removeItem(event.target)
            }
        }
    },
    keys: {}
});
AJS.ItemDescriptor = AJS.Descriptor.extend({
    REQUIRED_PROPERTIES: {
        label: true
    },
    _getDefaultOptions: function () {
        return {
            showLabel: true
        }
    },
    styleClass: function () {
        return this.properties.styleClass
    },
    value: function () {
        return this.properties.value
    },
    labelSuffix: function () {
        return this.properties.labelSuffix
    },
    title: function () {
        return this.properties.title
    },
    label: function () {
        return this.properties.label
    },
    allowDuplicate: function () {
        return this.properties.allowDuplicate
    },
    removeOnUnSelect: function () {
        return this.properties.removeOnUnSelect
    },
    icon: function () {
        return this.properties.icon
    },
    selected: function (value) {
        if (typeof value !== "undefined") {
            this.properties.selected = value
        }
        return this.properties.selected
    },
    model: function ($model) {
        if ($model) {
            this.properties.model = $model
        } else {
            return this.properties.model
        }
    },
    href: function () {
        return this.properties.href
    },
    html: function () {
        return this.properties.html
    }
});
AJS.GroupDescriptor = AJS.Descriptor.extend({
    _getDefaultOptions: function () {
        return {
            showLabel: true,
            label: "",
            items: []
        }
    },
    styleClass: function () {
        return this.properties.styleClass
    },
    weight: function () {
        return this.properties.weight
    },
    label: function () {
        return this.properties.label
    },
    showLabel: function () {
        return this.properties.showLabel
    },
    items: function () {
        return this.properties.items
    },
    addItem: function (item) {
        this.properties.items.push(item)
    },
    setModel: function ($model) {
        this.properties.model = $model
    },
    replace: function () {
        return this.properties.replace
    },
    description: function () {
        return this.properties.description
    },
    model: function ($model) {
        if ($model) {
            this.properties.model = $model
        } else {
            return this.properties.model
        }
    }
});
AJS.List = AJS.Control.extend({
    _getDefaultOptions: function () {
        return {
            matchingStrategy: "(^|.*\\s+)({0})(.*)",
            containerSelector: ".aui-list",
            itemSelector: "li"
        }
    },
    index: 0,
    moveToNext: function () {
        if (this.index < this.maxIndex) {
            this.unfocusAll();
            ++this.index;
            this.focus(this.SCROLL_DOWN)
        } else {
            if (this.$visibleItems.length > 1) {
                this.unfocusAll();
                this.index = 0;
                this.focus(this.SCROLL_DOWN)
            }
        }
        this.motionDetector.wait()
    },
    SCROLL_UP: -1,
    SCROLL_DOWN: 1,
    container: function (container) {
        if (container) {
            this.$container = AJS.$(container);
            this.containerSelector = container
        } else {
            return this.$container
        }
    },
    scrollContainer: function () {
        return this.container().parent()
    },
    unfocusAll: function () {
        this.$visibleItems.removeClass("active")
    },
    moveToPrevious: function () {
        if (this.index > 0) {
            this.unfocusAll();
            --this.index;
            this.focus(this.SCROLL_UP)
        } else {
            if (this.$visibleItems.length > 0) {
                this.unfocusAll();
                this.index = this.$visibleItems.length - 1;
                this.focus(this.SCROLL_UP)
            }
        }
        this.motionDetector.wait()
    },
    unfocus: function (direction) {
        if (direction !== undefined) {
            this.scrollTo(this.$visibleItems.eq(this.index), direction)
        }
        this.$visibleItems.eq(this.index).removeClass("active")
    },
    scrollTo: function ($target, direction) {
        var $scrollContainer = this.scrollContainer(),
            offsetTop = $target.offset().top - this.$container.offset().top;
        if ($target[0] === this.$visibleItems[0]) {
            $scrollContainer.scrollTop(0)
        } else {
            if ($scrollContainer.scrollTop() + $scrollContainer.height() < offsetTop + $target.outerHeight() || $scrollContainer.scrollTop() > offsetTop) {
                if (direction === -1) {
                    $scrollContainer.scrollTop(offsetTop)
                } else {
                    if (direction === 1) {
                        $scrollContainer.scrollTop(offsetTop + $target.outerHeight() - $scrollContainer.height())
                    }
                }
            }
        }
    },
    focus: function (direction) {
        var $target = this.$visibleItems.eq(this.index);
        if (direction !== undefined) {
            this.scrollTo($target, direction)
        }
        this.lastFocusedItemDescriptor = $target.data("descriptor");
        this.motionDetector.unbind();
        $target.addClass("active");
        if (!AJS.dim.dim) {
            $target.scrollIntoView({
                duration: 100,
                callback: AJS.$.proxy(this.motionDetector, "wait")
            })
        } else {
            this.motionDetector.wait()
        }
    },
    motionDetector: new JIRA.Mouse.MotionDetector(),
    disable: function () {
        if (this.disabled) {
            return
        }
        this._unassignEvents("document", document);
        this.disabled = true;
        this.lastFocusedItemDescriptor = null;
        this.motionDetector.unbind()
    },
    enable: function () {
        var instance = this;
        if (!instance.disabled) {
            return
        }
        instance.motionDetector.wait();
        window.setTimeout(function () {
            instance._assignEvents("document", document)
        }, 0);
        instance.disabled = false;
        this.scrollContainer().scrollTop(0)
    },
    getFocused: function () {
        return this.$visibleItems.filter(".active")
    },
    reset: function (index) {
        var noSuggestionsClassName = /(?:^|\s)no-suggestions(?!\S)/;
        var hiddenClassName = /(?:^|\s)hidden(?!\S)/;
        this.$container = AJS.$(this.options.containerSelector);
        this.items = AJS.$(this.options.itemSelector, this.$container).filter(function () {
            return !noSuggestionsClassName.test(this.className)
        });
        this.$visibleItems = this.items.filter(function () {
            return !hiddenClassName.test(this.className)
        });
        this.groups = AJS.$(this.options.groupSelector, this.$container);
        this.maxIndex = this.$visibleItems.length - 1;
        this.index = this.$visibleItems[index] ? index : 0;
        this.focus()
    },
    init: function (options) {
        options = options || {};
        if (options) {
            this.options = AJS.$.extend(true, this._getDefaultOptions(options), options)
        } else {
            this.options = this._getDefaultOptions(options)
        }
        var instance = this;
        this.containerSelector = AJS.$(this.options.$layerContent);
        this.disabled = true;
        this.reset();
        if (this.options.selectionHandler) {
            this.$container.delegate(this.options.itemSelector, "click", function (e) {
                instance.options.selectionHandler.call(instance, e)
            })
        }
        this.$container.delegate(this.options.itemSelector, "mouseover", function () {
            if (instance.motionDetector.moved && !instance.disabled) {
                instance.unfocusAll();
                instance.index = AJS.$.inArray(this, instance.$visibleItems);
                instance.focus()
            }
        })
    },
    _getLinkFromItem: function (item) {
        var link;
        item = AJS.$(item);
        if (item.is("a")) {
            link = item
        } else {
            link = item.find("a")
        }
        if (!link.length) {
            throw new Error("AJS.List._getLinkFromItem: could not find a link node")
        } else {
            return link
        }
    },
    generateListFromJSON: function (data, query) {
        var event, $result = AJS.$("<div>"),
            instance = this,
            ungrouped = [],
            $listItems;
        this.suggestions = 0;
        this.exactMatchIndex = -1;
        this.lastFocusedIndex = -1;
        this.lastQuery = query;
        AJS.$.each(data, function (i, descriptor) {
            if (descriptor instanceof AJS.GroupDescriptor) {
                if (ungrouped.length > 0) {
                    $result.append(instance._generateUngroupedOptions(ungrouped, query));
                    ungrouped = []
                }
                $result.append(instance._generateOptGroup(descriptor, query))
            } else {
                if (this instanceof AJS.ItemDescriptor) {
                    ungrouped.push(descriptor)
                }
            }
        });
        if (ungrouped.length > 0) {
            $result.append(this._generateUngroupedOptions(ungrouped, query))
        }
        if ($result.children().length === 0) {
            this.$container.html(this._render("noSuggestion"))
        } else {
            $result.find("ul:last").addClass("aui-last");
            this.$container.html($result.children())
        }
        this.$container.hide();
        $listItems = AJS.$("li > a", this.$container);
        $listItems.each(function () {
            var elem = AJS.$(this);
            elem.attr("title", elem.text())
        });
        $listItems.css({
            textOverflow: "ellipsis",
            overflow: "hidden"
        });
        this.$container.show();
        //$listItems.textOverflow("&#x2026;", false);
        AJS.trigger("contentChange", this.$container);
        this.reset(this.exactMatchIndex >= 0 ? this.exactMatchIndex : this.lastFocusedIndex)
    },
    _generateOption: function (item, query) {
        var replacementText;
        if (query) {
            var regexEscapedQuery = RegExp.escape(query),
                regex = new RegExp(AJS.format(this.options.matchingStrategy, regexEscapedQuery), "i");
            if (!regex.test(item.label())) {
                return null
            }
            replacementText = item.label().replace(regex, function (_, prefix, match, suffix) {
                return AJS.$("<div>").append(AJS.$("<span>").text(prefix)).append(AJS.$("<em>").text(match)).append(AJS.$("<span>").text(suffix)).html()
            })
        }
        if (this.exactMatchIndex < 0) {
            var itemValue = AJS.$.trim(item.label()).toLowerCase();
            if (itemValue === AJS.$.trim(query).toLowerCase()) {
                this.exactMatchIndex = this.suggestions
            } else {
                if (this.lastFocusedIndex < 0 && this.lastFocusedItemDescriptor && itemValue === AJS.$.trim(this.lastFocusedItemDescriptor.label()).toLowerCase()) {
                    this.lastFocusedIndex = this.suggestions
                }
            }
        }
        this.suggestions++;
        return this._render("suggestion", item, replacementText)
    },
    _generateUngroupedOptions: function (options, query) {
        var hasSuggestion = false,
            instance = this,
            $container = this._render("ungroupedSuggestions");
        AJS.$.each(options, function (_, option) {
            var $suggestion = instance._generateOption(option, query);
            if ($suggestion) {
                hasSuggestion = true;
                $container.append($suggestion)
            }
        });
        if (hasSuggestion) {
            return $container
        }
    },
    _generateOptGroup: function (groupDescriptor, query) {
        var res = AJS.$(),
            hasSuggestion, instance = this,
            optContainer = this._render("suggestionGroup", groupDescriptor);
        AJS.$.each(groupDescriptor.items(), function (i, option) {
            var suggestion = instance._generateOption(option, query);
            if (suggestion) {
                hasSuggestion = true, optContainer.append(suggestion)
            }
        });
        if (!hasSuggestion) {
            return
        }
        if (groupDescriptor.showLabel() !== false) {
            res = res.add(this._render("suggestionGroupHeading", groupDescriptor))
        }
        res = res.add(optContainer);
        return res
    },
    _events: {
        document: {
            keydown: function (e) {
                this._handleKeyEvent(e)
            },
            keypress: function (e) {
                this._handleKeyEvent(e)
            }
        }
    },
    _renders: {
        suggestion: function (descriptor, replacementText) {
            var listElem = AJS.$('<li class="aui-list-item aui-list-item-' + AJS.$.trim(descriptor.label().toLowerCase()).replace(/[\s\.]+/g, "-") + '">'),
                linkElem = AJS.$("<a />").addClass("aui-list-item-link");
            if (descriptor.selected()) {
                listElem.addClass("aui-checked")
            }
            linkElem.attr("href", descriptor.href() || "#");
            if (descriptor.icon() && descriptor.icon() !== "none") {
                linkElem.addClass("aui-iconised-link").css({
                    backgroundImage: "url(" + descriptor.icon() + ")"
                })
            }
            if (descriptor.styleClass()) {
                linkElem.addClass(descriptor.styleClass())
            }
            if (descriptor.html()) {
                linkElem.html(descriptor.html())
            } else {
                if (!replacementText) {
                    linkElem.text(descriptor.label())
                } else {
                    linkElem.html(replacementText)
                }
            }
            if (descriptor.labelSuffix()) {
                AJS.$("<span class='aui-item-suffix' />").text(descriptor.labelSuffix()).appendTo(linkElem)
            }
            listElem.append(linkElem).data("descriptor", descriptor);
            return listElem
        },
        noSuggestion: function () {
            return AJS.$("<div class='no-suggestions'><span style='font-style:oblique'>" + AJS.params.frotherNomatches + "</span></div>")
        },
        ungroupedSuggestions: function () {
            return AJS.$("<ul>")
        },
        suggestionGroup: function (descriptor) {
            return AJS.$("<ul class='aui-list-section' />").attr("id", descriptor.label().replace(/\s/g, "-").toLowerCase()).addClass(descriptor.styleClass()).data("descriptor", descriptor)
        },
        suggestionGroupHeading: function (descriptor) {
            var elem = AJS.$("<h5 />").text(descriptor.label()).addClass(descriptor.styleClass()).data("descriptor", descriptor);
            if (descriptor.description()) {
                AJS.$("<span class='aui-section-description' />").text(" (" + descriptor.description() + ")").appendTo(elem)
            }
            return elem
        }
    },
    _acceptSuggestion: function (item) {
        if (!item instanceof AJS.$) {
            item = AJS.$(item)
        }
        var linkNode = this._getLinkFromItem(item);
        var event = new jQuery.Event("click");
        linkNode.trigger(event, [linkNode]);
        if (!event.isDefaultPrevented()) {
            window.location.href = linkNode.attr("href")
        }
    },
    _acceptUserInput: function ($field) {
        $field.triggerHandler("blur")
    },
    _handleSectionByKeyboard: function (e) {
        var $focusedItem = this.getFocused();
        var $field = AJS.$(e.target);
        if ($focusedItem.length === 0) {
            return
        }
        if ($focusedItem.closest("#user-inputted-option").length > 0) {
            this._acceptUserInput($field);
            return
        }
        if (this._latestQuery && $field.val() !== this._latestQuery) {
            var inputWords = $field.val().toLowerCase().match(/\S+/g);
            if (inputWords) {
                var html = this.lastFocusedItemDescriptor && this.lastFocusedItemDescriptor.html();
                var $item = html ? AJS.$("<div>").html(html) : $focusedItem;
                var matches = AJS.$.map($item.find("em,b"), function ($match) {
                    $match = AJS.$($match);
                    return ($match.text() + AJS.$($match.attr("nextSibling")).text().match(/^\S*/)[0]).toLowerCase()
                });
                for (var i = 0; i < inputWords.length; i++) {
                    var word = inputWords[i];
                    var n = word.length;
                    var hasMatch = false;
                    for (var j = 0; j < matches.length; j++) {
                        if (matches[j].slice(0, n) === word) {
                            hasMatch = true;
                            break
                        }
                    }
                    if (!hasMatch) {
                        this._acceptUserInput($field);
                        return
                    }
                }
            }
        }
        if (this.options.selectionHandler && !this.options.selectionHandler.call(this, e)) {
            return
        }
        this._acceptSuggestion($focusedItem)
    },
    _isValidInput: function () {
        return !this.disabled && this.$container.is(":visible")
    },
    keys: {
        down: function (e) {
            this.moveToNext();
            e.preventDefault()
        },
        up: function (e) {
            this.moveToPrevious();
            e.preventDefault()
        },
        "return": function (e) {
            this._handleSectionByKeyboard(e)
        }
    }
});
AJS.Dropdown = AJS.Control.extend({
    CLASS_SIGNATURE: "AJS_DROPDOWN",
    init: function (options) {
        var instance = this;
        if (!(options instanceof AJS.Dropdown.OptionsDescriptor)) {
            this.options = new AJS.Dropdown.OptionsDescriptor(options)
        } else {
            this.options = options
        }
        this.layerController = new AJS.InlineLayer(this.options.allProperties());
        this.listController = this.options.listController();
        this.listEnabler = function (e) {
            instance.listController._handleKeyEvent(e)
        };
        this.layerController.onhide(function () {
            instance.hide()
        });
        this.layerController.contentChange(function () {
            instance.listController.removeAllItems();
            instance.layerController.layer().find("li").each(function () {
                instance.listController.addItem(new AJS.Dropdown.ListItem({
                    element: this
                }))
            });
            if (instance.options.focusFirstItem()) {
                instance.listController.shiftFocus(0)
            } else {
                instance.listController.prepareForInput()
            }
        });
        this.trigger(this.options.trigger());
        this._applyIdToLayer()
    },
    show: function () {
        var instance = this;
        this.trigger().addClass(AJS.ACTIVE_CLASS);
        this.layerController.show();
        if (this.options.focusFirstItem()) {
            this.listController.shiftFocus(0)
        } else {
            this.listController.prepareForInput()
        }
    },
    hide: function () {
        this.trigger().removeClass(AJS.ACTIVE_CLASS);
        this.layerController.hide();
        this.listController.trigger("blur")
    },
    toggle: function () {
        if (this.layerController.isVisible()) {
            this.hide()
        } else {
            this.show()
        }
    },
    content: function (content) {
        if (content) {
            this.layerController.content(content)
        } else {
            return this.layerController.content()
        }
    },
    trigger: function (trigger) {
        if (trigger) {
            if (this.options.trigger()) {
                this._unassignEvents("trigger", this.options.trigger())
            }
            this.options.trigger(AJS.$(trigger));
            if (!this.layerController.offsetTarget()) {
                this.layerController.offsetTarget(this.options.trigger())
            }
            this._assignEvents("trigger", this.options.trigger())
        } else {
            return this.options.trigger()
        }
    },
    _applyIdToLayer: function () {
        if (this.trigger().attr("id")) {
            this.layerController.layer().attr("id", this.trigger().attr("id") + "_drop")
        }
    },
    _events: {
        trigger: {
            click: function (e) {
                e.preventDefault();
                this.toggle()
            }
        }
    }
});
AJS.Dropdown.TRIGGER_SELECTOR = ".aui-dropdown-trigger";
AJS.Dropdown.CONTENT_SELECTOR = ".aui-dropdown-content";
AJS.DropDown = AJS.Dropdown;
AJS.Dropdown.create = function (options) {
    var dropdowns = [];
    if (options.content && !options.trigger) {
        options.content = AJS.$(options.content);
        AJS.$.each(options.content, function () {
            var instanceOptions = AJS.copyObject(options);
            instanceOptions.content = AJS.$(this);
            dropdowns.push(new AJS.Dropdown(instanceOptions))
        })
    } else {
        if (!options.content && options.trigger) {
            options.trigger = AJS.$(options.trigger);
            AJS.$.each(options.trigger, function () {
                var instanceOptions = AJS.copyObject(options);
                instanceOptions.trigger = AJS.$(this);
                dropdowns.push(new AJS.Dropdown(instanceOptions))
            })
        } else {
            if (options.content && options.trigger) {
                options.content = AJS.$(options.content);
                options.trigger = AJS.$(options.trigger);
                if (options.content.length === options.trigger.length) {
                    options.trigger.each(function (i) {
                        var instanceOptions = AJS.copyObject(options);
                        instanceOptions.trigger = AJS.$(this);
                        instanceOptions.content = options.content.eq(i);
                        dropdowns.push(new AJS.Dropdown(instanceOptions))
                    })
                } else {
                    throw new Error("AJS.Dropdown.create: Expected the same number of content elements as trigger elements")
                }
            }
        }
    }
    return dropdowns
};
AJS.Dropdown.OptionsDescriptor = AJS.Descriptor.extend({
    init: function (properties) {
        this._super(properties);
        if (!this.content() && !this.trigger()) {
            throw new Error("AJS.Dropdown.OptionsDescriptor: expected either [content] or [trigger] to be defined.")
        }
        if (this.trigger() && !this.content()) {
            this.content(this.trigger().next(AJS.Dropdown.CONTENT_SELECTOR))
        } else {
            if (this.content() && !this.trigger()) {
                this.content(this.trigger().next(AJS.Dropdown.TRIGGER_SELECTOR))
            }
        }
        if (this.trigger() && !this.content()) {
            if (!this.ajaxOptions()) {
                if (this.trigger().attr("href")) {
                    this.ajaxOptions(this.trigger().attr("href"))
                }
            } else {
                if (!this.ajaxOptions().url) {
                    this.ajaxOptions().url = this.trigger().attr("href")
                }
            }
            this.contentRetriever(new AJS.AjaxContentRetriever(this.ajaxOptions()))
        } else {
            if (this.content()) {
                this.contentRetriever(new AJS.DOMContentRetriever(this.content()))
            }
        }
        if (!this.listController()) {
            this.listController(new AJS.Dropdown.ListItemGroup())
        }
    },
    _getDefaultOptions: function () {
        return {
            trigger: null,
            ajaxOptions: null
        }
    },
    content: function (content) {
        if (content) {
            content = AJS.$(content);
            if (content.length) {
                this.properties.content = content
            }
        } else {
            return this.properties.content
        }
    },
    trigger: function (trigger) {
        if (trigger) {
            this.properties.trigger = trigger
        } else {
            return this.properties.trigger
        }
    },
    contentRetriever: function (contentRetriever) {
        if (contentRetriever) {
            this.properties.contentRetriever = contentRetriever
        } else {
            return this.properties.contentRetriever
        }
    },
    listController: function (listController) {
        if (listController) {
            this.properties.listController = listController
        } else {
            return this.properties.listController
        }
    },
    focusFirstItem: function (focusFirstItem) {
        if (focusFirstItem) {
            this.properties.focusFirstItem = focusFirstItem
        } else {
            return this.properties.focusFirstItem
        }
    },
    ajaxOptions: function (ajaxOptions) {
        if (ajaxOptions) {
            this.properties.ajaxOptions = ajaxOptions
        } else {
            return this.properties.ajaxOptions
        }
    },
    loop: function (loop) {
        if (typeof loop !== "undefined") {
            this.properties.loop = loop
        } else {
            return this.properties.loop
        }
    },
    alignment: function (alignment) {
        if (alignment) {
            this.properties.alignment = alignment
        } else {
            return this.properties.alignment
        }
    },
    eventDelegator: function (eventDelegator) {
        if (typeof eventDelegator !== "undefined") {
            this.properties.eventDelegator = eventDelegator
        } else {
            return this.properties.eventDelegator
        }
    }
});
AJS.Dropdown.ListItem = AJS.Control.extend({
    init: function (options) {
        this._setOptions(options);
        this.$element = AJS.$(this.options.element);
        this.hasFocus = false;
        this._assignEvents("instance", this);
        this._assignEvents("element", this.$element)
    },
    _getDefaultOptions: function () {
        return {
            element: null,
            focusClass: AJS.ACTIVE_CLASS
        }
    },
    _events: {
        "instance": {
            "focus": function (event) {
                this.hasFocus = true;
                this.$element.addClass(this.options.focusClass);
                if (!event.noscrolling) {
                    AJS.Dropdown.ListItem.MOTION_DETECTOR.unbind();
                    this.isWaitingForMove = true;
                    this.$element.scrollIntoView(AJS.Dropdown.ListItem.SCROLL_INTO_VIEW_OPTIONS)
                }
            },
            "blur": function () {
                this.hasFocus = false;
                this.$element.removeClass(this.options.focusClass)
            },
            "accept": function () {
                var event = new jQuery.Event("click");
                var $target = this.$element.is("a[href]") ? this.$element : this.$element.find("a[href]");
                $target.trigger(event);
                if (!event.isDefaultPrevented()) {
                    window.top.location = $target.attr("href")
                }
            }
        },
        "element": {
            "mousemove": function () {
                if (((this.isWaitingForMove && AJS.Dropdown.ListItem.MOTION_DETECTOR.moved) && !this.hasFocus) || !this.hasFocus) {
                    this.isWaitingForMove = false;
                    this.trigger({
                        type: "focus",
                        noscrolling: true
                    })
                }
            }
        }
    }
});
AJS.Dropdown.ListItem.MOTION_DETECTOR = new JIRA.Mouse.MotionDetector();
AJS.Dropdown.ListItem.SCROLL_INTO_VIEW_OPTIONS = {
    duration: 100,
    callback: function () {
        AJS.Dropdown.ListItem.MOTION_DETECTOR.wait()
    }
};
AJS.Dropdown.ListItemGroup = AJS.Group.extend({
    keys: {
        "up": function (event) {
            this.shiftFocus(-1);
            event.preventDefault()
        },
        "down": function (event) {
            this.shiftFocus(1);
            event.preventDefault()
        },
        "return": function (event) {
            this.items[this.index].trigger("accept");
            event.preventDefault()
        }
    }
});

/*
 * Class: 		JIRA.Dropdown
 * Purpose: 	Base Class of  dropdown
 * namespace:   JIRA.Dropdown
 */
JIRA.Dropdown = function () {
    var instances = [];
    return {
        addInstance: function () {
            instances.push(this)
        },
        hideInstances: function () {
            var that = this;
            jQuery(instances).each(function () {
                if (that !== this) {
                    this.hideDropdown()
                }
            })
        },
        getHash: function () {
            if (!this.hash) {
                this.hash = {
                    container: this.dropdown,
                    hide: this.hideDropdown,
                    show: this.displayDropdown
                }
            }
            return this.hash
        },
        displayDropdown: function () {
            if (JIRA.Dropdown.current === this) {
                return
            }
            this.hideInstances();
            JIRA.Dropdown.current = this;
            this.dropdown.css({
                display: "block"
            });
            this.displayed = true;
            var dd = this.dropdown;
            setTimeout(function () {
                var win = jQuery(window);
                var minScrollTop = dd.offset().top + dd.attr("offsetHeight") - win.height() + 10;
                if (win.scrollTop() < minScrollTop) {
                    jQuery("html,body").animate({
                        scrollTop: minScrollTop
                    }, 300, "linear")
                }
            }, 100)
        },
        hideDropdown: function () {
            if (this.displayed === false) {
                return
            }
            JIRA.Dropdown.current = null;
            this.dropdown.css({
                display: "none"
            });
            this.displayed = false
        },
        init: function (trigger, dropdown) {
            var that = this;
            this.addInstance(this);
            this.dropdown = jQuery(dropdown);
            this.dropdown.css({
                display: "none"
            });
            jQuery(document).keydown(function (e) {
                if (e.keyCode === 9) {
                    that.hideDropdown()
                }
            });
            if (trigger.target) {
                jQuery.aop.before(trigger, function () {
                    if (!that.displayed) {
                        that.displayDropdown()
                    }
                })
            } else {
                that.dropdown.css("top", jQuery(trigger).outerHeight() + "px");
                trigger.click(function (e) {
                    if (!that.displayed) {
                        that.displayDropdown();
                        e.stopPropagation()
                    } else {
                        that.hideDropdown()
                    }
                    e.preventDefault()
                })
            }
			
			// when user click on any area except active section
			// the active area also should be hide
            jQuery(document.body).click(function () {
                if (that.displayed) {
                    that.hideDropdown()
                }
            })
        }
    }
}();
JIRA.Dropdown.Standard = function (trigger, dropdown) {
    var that = begetObject(JIRA.Dropdown);
    that.init(trigger, dropdown);
    return that
};
JIRA.Dropdown.AutoComplete = function (trigger, dropdown) {
    var that = begetObject(JIRA.Dropdown);
    that.init = function (trigger, dropdown) {
        this.addInstance(this);
        this.dropdown = jQuery(dropdown).click(function (e) {
            e.stopPropagation()
        });
        this.dropdown.css({
            display: "none"
        });
        if (trigger.target) {
            jQuery.aop.before(trigger, function () {
                if (!that.displayed) {
                    that.displayDropdown()
                }
            })
        } else {
            trigger.click(function (e) {
                if (!that.displayed) {
                    that.displayDropdown();
                    e.stopPropagation()
                }
            })
        }
        jQuery(document.body).click(function () {
            if (that.displayed) {
                that.hideDropdown()
            }
        })
    };
    that.init(trigger, dropdown);
    return that
};
AJS.namespace("jira.widget.dropdown", null, JIRA.Dropdown);
JIRA.containDropdown = function (dropdown, containerSelector, dynamic) {
    function getDropdownOffset() {
        return dropdown.$.offset().top - jQuery(containerSelector).offset().top
    }
    var container, ddOffset, availableArea, shadowOffset = 25;
    if (dropdown.$.parents(containerSelector).length !== -1) {
        container = jQuery(containerSelector), ddOffset = getDropdownOffset(), shadowOffset = 30, availableArea = container.outerHeight() - ddOffset - shadowOffset;
        if (availableArea <= parseInt(dropdown.$.attr("scrollHeight"), 10)) {
            JIRA.containDropdown.containHeight(dropdown, availableArea)
        } else {
            if (dynamic) {
                JIRA.containDropdown.releaseContainment(dropdown)
            }
        }
        dropdown.reset()
    }
};
JIRA.containDropdown.containHeight = function (dropdown, availableArea) {
    dropdown.$.css({
        height: availableArea
    });
    if (dropdown.$.css("overflowY") !== "scroll") {
        dropdown.$.css({
            width: 15 + dropdown.$.attr("scrollWidth"),
            overflowY: "scroll",
            overflowX: "hidden"
        })
    }
};
JIRA.containDropdown.releaseContainment = function (dropdown) {
    dropdown.$.css({
        height: "",
        width: "",
        overflowY: "",
        overflowX: ""
    })
};
AJS.namespace("AJS.containDropdown", null, JIRA.containDropdown);
AJS.$.deactivateLinkedMenu = function () {};
AJS.$.linkedMenuInstances = [];
AJS.$.fn.linkedMenu = function (opts) {
    var idx, that = this,
        onDisable, enabled = false,
        focusElement = function (elem) {
            elem = AJS.$(elem);
            that.blur();
            elem.trigger("click", "focus", "mousedown")
        },
        keyHandler = function (e) {
            var targ;
            if (e.keyCode === 37 || e.keyCode === 39 || e.keyCode === 27) {
                if (e.keyCode === 37) {
                    targ = idx - 1;
                    if (idx - 1 >= 0) {
                        if (isNotActive(that[targ])) {
                            idx = targ;
                            focusElement(that[idx])
                        }
                    } else {
                        targ = that.length - 1;
                        if (isNotActive(that[targ])) {
                            idx = targ;
                            focusElement(that[idx])
                        }
                    }
                } else {
                    if (e.keyCode === 39) {
                        targ = idx + 1;
                        if (targ < that.length) {
                            if (isNotActive(that[targ])) {
                                idx = targ;
                                focusElement(that[idx])
                            }
                        } else {
                            targ = 0;
                            if (isNotActive(that[targ])) {
                                idx = targ;
                                focusElement(that[idx])
                            }
                        }
                    } else {
                        that.disableLinkedMenu(e)
                    }
                }
                e.preventDefault()
            }
        },
        isNotActive = function (elem) {
            if (elem !== that[idx]) {
                return true
            }
        },
        focusBridge = function () {
            if (isNotActive(this)) {
                idx = AJS.$.inArray(this, that);
                focusElement(this)
            }
        },
        reflectionBridge = function () {
            var targ = AJS.$.inArray(this, AJS.$(opts.reflectFocus));
            if (isNotActive(that[targ])) {
                idx = targ;
                focusElement(that[idx])
            }
        },
        enable = function () {
            var elem, clss;
            if (!enabled) {
                AJS.$.currentLinkedMenu = that;
                if (opts.onFocusRemoveClass) {
                    elem = AJS.$(opts.onFocusRemoveClass);
                    clss = opts.onFocusRemoveClass.match(/\.([a-z]*)$/);
                    if (clss && clss[1] && elem.length > 0) {
                        AJS.$(opts.onFocusRemoveClass).removeClass(clss[1]);
                        onDisable = function () {
                            AJS.$(elem).addClass(clss[1])
                        }
                    }
                }
                enabled = true;
                idx = AJS.$.inArray(this, that);
                that.mouseover(focusBridge);
                if (AJS.$.browser.mozilla) {
                    AJS.$(document).keypress(keyHandler)
                } else {
                    AJS.$(document).keydown(keyHandler)
                }
                AJS.$(document).mousedown(that.disableLinkedMenu);
                if (opts.reflectFocus) {
                    AJS.$(opts.reflectFocus).mouseover(reflectionBridge)
                }
            }
        };
    that.disableLinkedMenu = function (e) {
        AJS.$(document).unbind("keypress", keyHandler);
        AJS.$(document).unbind("keydown", keyHandler);
        that.unbind("mouseover", focusBridge);
        AJS.$(document).unbind("mousedown", arguments.callee);
        if (opts.reflectFocus) {
            AJS.$(opts.reflectFocus).unbind("mouseover", reflectionBridge)
        }
        if (onDisable) {
            onDisable()
        }
        that.blur();
        delete AJS.$.currentLinkedMenu;
        window.setTimeout(function () {
            enabled = false
        }, 200)
    };
    opts = opts || {};
    that.click(enable);
    return that
};
jQuery.fn.scrollIntoView = function (options) {
    if (this.length > 0 && !this.hasFixedParent()) {
        options = options || {};
        options.marginTop = options.marginTop || options.margin || 0;
        options.marginBottom = options.marginBottom || options.margin || 0;
        if (!this.is(":visible") && options.callback) {
            options.callback();
            return this
        }
        var $window = window.top.jQuery(window.top);
        var $stalker = window.top.jQuery("#stalker");
        var scrollTop = $window.scrollTop();
        var scrollHeight = $window.height();
        var offsetTop = Math.max(0, getPageY(this[0]) - options.marginTop);
        var offsetHeight = options.marginTop + this.outerHeight() + options.marginBottom;
        var newScrollTop = scrollTop;
        if (newScrollTop + scrollHeight < offsetTop + offsetHeight) {
            newScrollTop = offsetTop + offsetHeight - scrollHeight
        }
        if ($stalker.length > 0) {
            offsetTop -= $stalker.outerHeight() + 35
        }
        if (newScrollTop > offsetTop) {
            newScrollTop = offsetTop
        }
        if (newScrollTop !== scrollTop) {
            var $target = this;
            var $document = window.top.jQuery(window.top.document);
            $document.trigger("moveToStarted", $target);
            $document.find("body, html").stop(true).animate({
                scrollTop: newScrollTop
            }, options.duration || 100, "swing", function () {
                if (options.callback) {
                    options.callback()
                }
                $document.trigger("moveToFinished", $target);
                $stalker.trigger("positionChanged")
            })
        } else {
            if (options.callback) {
                options.callback()
            }
        }
    }
    return this;

    function getPageY(element) {
        var offsetTop = 0;
        do {
            offsetTop += element.offsetTop
        } while (element = element.offsetParent);
        return offsetTop
    }
};
AJS.namespace("JIRA.FRAGMENTS");
JIRA.FRAGMENTS.issueActionsFragment = function () {
    function addIssueIdToReturnUrl(issueId) {
        var matchSelectedIssueId = /selectedIssueId=[0-9]*/g;
        if (self != top) {
            return encodeURIComponent(window.top.location.href)
        }
        var url = window.location.href,
            newUrl = url;
        if (/selectedIssueId=[0-9]*/.test(url)) {
            newUrl = url.replace(matchSelectedIssueId, "selectedIssueId=" + issueId)
        } else {
            if (url.lastIndexOf("?") >= 0) {
                newUrl = url + "&"
            } else {
                newUrl = url + "?"
            }
            newUrl = newUrl + "selectedIssueId=" + issueId
        }
        return encodeURIComponent(newUrl)
    }
    return function (json) {
        var returnURL = addIssueIdToReturnUrl(json.id);
        var htmlParts = ['<div class="aui-list"><ul class="aui-list-section"><li class="aui-list-item"><a href="', contextPath, "/browse/", json.key, '" class="aui-list-item-link">', htmlEscape(json.viewIssue), "</a></li></ul>"];
        var hasActions = json.actions && json.actions.length > 0;
        var hasOperations = json.operations && json.operations.length > 0;
        if (hasActions) {
            htmlParts.push(hasOperations ? '<ul class="aui-list-section">' : '<ul class="aui-list-section aui-last">');
            var URL_A = contextPath + "/secure/WorkflowUIDispatcher.jspa?id=" + json.id + "&amp;action=";
            var URL_B = "&amp;atl_token=" + json.atlToken + "&amp;returnUrl=" + returnURL;
            AJS.$.each(json.actions, function () {
                htmlParts.push('<li class="aui-list-item"><a href="', URL_A, this.action, URL_B, '" rel="', this.action, '" class="aui-list-item-link issueaction-workflow-transition">', htmlEscape(this.name), "</a></li>")
            });
            htmlParts.push("</ul>")
        }
        if (hasOperations) {
            htmlParts.push('<ul class="aui-list-section aui-last">');
            URL_A = "&amp;returnUrl=" + returnURL;
            URL_B = "&amp;atl_token=" + json.atlToken;
            AJS.$.each(json.operations, function () {
                htmlParts.push('<li class="aui-list-item"><a href="', this.url, URL_A, URL_B, '" class="aui-list-item-link ', this.styleClass, '">', htmlEscape(this.name), "</a></li>")
            });
            htmlParts.push("</ul>")
        }
        htmlParts.push("</div>");
        return AJS.$(htmlParts.join(""))
    }
}();
AJS.DropdownSelect = AJS.Control.extend({
    init: function (options) {
        var instance = this;
        this.model = new AJS.SelectModel(options);
        this.model.$element.hide();
        this._createFurniture();
        this.dropdownController = AJS.InlineLayer.create({
            alignment: AJS.LEFT,
            width: 200,
            content: AJS.$(".aui-list", this.$container)
        });
        this.dropdownController.layer().addClass("select-menu");
        this.listController = new AJS.List({
            containerSelector: AJS.$(".aui-list", this.$container),
            groupSelector: "ul.opt-group",
            itemSelector: "li:not(.no-suggestions)",
            selectionHandler: function (e) {
                instance._selectionHandler(this.getFocused(), e);
                e.preventDefault()
            }
        });
        this._assignEventsToFurniture()
    },
    show: function () {
        this.dropdownController.show();
        this._resetSuggestions();
        this.listController.enable()
    },
    _assignEventsToFurniture: function () {
        this._assignEvents("trigger", this.$trigger)
    },
    _createFurniture: function () {
        var id = this.model.$element.attr("id");
        this.$container = this._render("container", id);
        this.$trigger = this.model.$element.prev("a").appendTo(this.$container);
        this.$container.append(this._render("suggestionsContainer", id));
        this.$container.insertBefore(this.model.$element)
    },
    _resetSuggestions: function () {
        this.listController.generateListFromJSON(this.model.getAllDescriptors());
        this.listController.unfocusAll();
        this.listController.index = 0;
        this.listController.focus()
    },
    _renders: {
        container: function (idPrefix) {
            return AJS.$('<div class="select-menu" id="' + idPrefix + '-multi-select">')
        },
        suggestionsContainer: function (idPrefix) {
            return AJS.$('<div class="aui-list aui-list-checked" id="' + idPrefix + '-suggestions" tabindex="-1"></div>')
        }
    },
    _selectionHandler: function (selected) {
        var instance = this,
            intCount = 0;
        this.model.setSelected(selected.data("descriptor"));
        this.dropdownController.content().find(".aui-checked").removeClass(".aui-checked");
        selected.addClass(".aui-checked");
        var myInterval = window.setInterval(function () {
            intCount++;
            selected.toggleClass(".aui-checking");
            if (intCount > 2) {
                clearInterval(myInterval);
                instance.dropdownController.hide()
            }
        }, 80)
    },
    _events: {
        trigger: {
            click: function (e) {
                this.show();
                e.preventDefault();
                e.stopPropagation()
            }
        }
    }
});
AJS.namespace("AJS.SelectMenu", null, AJS.DropdownSelect);
AJS.SecurityLevelSelect = AJS.DropdownSelect.extend({
    _createFurniture: function () {
        AJS.populateParameters();
        this._super()
    },
    _selectionHandler: function (selected) {
        var descriptor = selected.data("descriptor");
        if (descriptor && !descriptor.value()) {
            this.$trigger.find("span:first").removeClass("icon-locked").addClass("icon-unlocked");
            this.$container.parent().find(".current-level").text(AJS.params.securityLevelViewableByAll)
        } else {
            this.$trigger.find("span:first").removeClass("icon-unlocked").addClass("icon-locked");
            var htmlEscapedLabel = AJS.$("<div/>").text(descriptor.label()).html();
            this.$container.parent().find(".current-level").html(AJS.format(AJS.params.securityLevelViewableRestrictedTo, htmlEscapedLabel))
        }
        this._super(selected)
    },
    _handleDownKey: function (e) {
        if (e.keyCode === jQuery.ui.keyCode.DOWN && !this.dropdownController.isVisible()) {
            e.preventDefault();
            e.stopPropagation();
            this.show()
        }
    },
    _events: {
        trigger: {
            keydown: function (e) {
                this._handleDownKey(e)
            },
            keypress: function (e) {
                this._handleDownKey(e)
            }
        }
    }
});
AJS.namespace("AJS.SecurityLevel", null, AJS.SecurityLevelSelect);
AJS.SelectModel = AJS.Control.extend({
    init: function (options) {
        if (options.element) {
            options.element = AJS.$(options.element)
        } else {
            options.element = AJS.$(options)
        }
        this._setOptions(options);
        this.$element = this.options.element;
        this.type = this.$element.attr("multiple") ? "multiple" : "single";
        this._parseDescriptors()
    },
    _getDefaultOptions: function () {
        return {}
    },
    setSelected: function (descriptor) {
        var selectedItem = false;
        if (this.type === "single") {
            this.setAllUnSelected()
        }
        this.$element.find("option").filter(function () {
            return AJS.$(this).attr("value") === descriptor.value()
        }).each(function () {
            selectedItem = true;
            AJS.$(this).attr("selected", "selected").data("descriptor").selected(true)
        });
        if (!selectedItem) {
            descriptor.selected(true);
            var newOption = this._render("option", descriptor);
            newOption.attr("selected", "selected");
            this.$element.append(newOption)
        }
    },
    setAllUnSelected: function () {
        var instance = this;
        AJS.$(this.getSelectedDescriptors()).each(function () {
            instance.setUnSelected(this)
        })
    },
    setUnSelected: function (descriptor) {
        var instance = this;
        this.$element.find("option").filter(function () {
            return AJS.$(this).attr("value") === descriptor.value()
        }).each(function () {
            var $this = AJS.$(this);
            if (instance.options.removeOnUnSelect || $this.data("descriptor").removeOnUnSelect()) {
                $this.remove()
            } else {
                $this.attr("selected", "");
                $this.data("descriptor").selected(false)
            }
        })
    },
    _isOptionPresent: function (descriptor, ctx) {
        var notFound = true;
        var value = descriptor.value();
        AJS.$("option", ctx || this.$element).each(function () {
            return notFound = (this.value !== value)
        });
        return !notFound
    },
    _isOptionGroupPresent: function (descriptor) {
        var $optgroup = this.$element.find("optgroup").filter(function () {
            return AJS.$(this).attr("label") === descriptor.label()
        });
        return $optgroup.length > 0
    },
    remove: function (descriptor) {
        if (descriptor && descriptor.model()) {
            descriptor.model().remove()
        }
    },
    getDescriptor: function (value) {
        var returnDescriptor;
        value = AJS.$.trim(value.toLowerCase());
        AJS.$.each(this.getAllDescriptors(false), function (e, descriptor) {
            if (value === AJS.$.trim(descriptor.label().toLowerCase()) || value === AJS.$.trim(descriptor.value().toLowerCase())) {
                returnDescriptor = descriptor;
                return false
            }
        });
        return returnDescriptor
    },
    appendOptionsFromJSON: function (optionDescriptors) {
        var instance = this;
        AJS.$.each(optionDescriptors, function (i, descriptor) {
            var optgroup;
            if (descriptor instanceof AJS.GroupDescriptor && (descriptor.replace() || !instance._isOptionGroupPresent(descriptor))) {
                if (descriptor.replace()) {
                    optgroup = instance.$element.find('optgroup[label="' + descriptor.label() + '"]');
                    if (optgroup.length) {
                        optgroup.find("option:not(:selected)").remove()
                    }
                }
                if (!optgroup || !optgroup.length) {
                    optgroup = instance._render("optgroup", descriptor)
                }
                optgroup.data("descriptor", descriptor);
                AJS.$.each(descriptor.items(), function (i, optDescriptor) {
                    if (!instance._isOptionPresent(optDescriptor, optgroup)) {
                        optgroup.append(instance._render("option", optDescriptor))
                    }
                });
                if (typeof descriptor.weight() !== "undefined") {
                    var target = instance.$element.children().eq(descriptor.weight());
                    if (target[0] !== optgroup[0]) {
                        if (target.length) {
                            optgroup.insertBefore(target)
                        } else {
                            optgroup.appendTo(instance.$element)
                        }
                    }
                } else {
                    optgroup.appendTo(instance.$element)
                }
            } else {
                if (descriptor instanceof AJS.GroupDescriptor) {
                    optgroup = instance.$element.find('optgroup[label="' + descriptor.label() + '"]');
                    optgroup.data("descriptor", descriptor);
                    AJS.$.each(descriptor.items(), function (i, optDescriptor) {
                        if (!instance._isOptionPresent(optDescriptor, optgroup)) {
                            optgroup.append(instance._render("option", optDescriptor))
                        }
                    })
                } else {
                    if (descriptor instanceof AJS.ItemDescriptor && !instance._isOptionPresent(descriptor)) {
                        instance._render("option", descriptor).appendTo(instance.$element)
                    }
                }
            }
        })
    },
    _parseOption: function (optionElem) {
        var descriptor;
        optionElem = AJS.$(optionElem);
        if (this.options.removeNullOptions && this._hasNullValue(optionElem)) {
            optionElem.remove();
            return null
        }
        descriptor = new AJS.ItemDescriptor({
            value: optionElem.val(),
            title: optionElem.attr("title"),
            label: optionElem.text(),
            icon: optionElem.css("backgroundImage"),
            selected: optionElem.attr("selected") ? true : false,
            model: optionElem
        });
        optionElem.data("descriptor", descriptor);
        return descriptor
    },
    _hasNullValue: function (optionElement) {
        return optionElement.val() < 0
    },
    _parseDescriptors: function () {
        var instance = this,
            items = this.$element.children();

        function parseOptGroup(optionGroup) {
            optionGroup.data("descriptor", new AJS.GroupDescriptor({
                label: optionGroup.attr("label"),
                styleClass: optionGroup.attr("className"),
                model: optionGroup,
                items: retrieveAvailableOptions(optionGroup)
            }))
        }
        function retrieveAvailableOptions(parent) {
            var availableOptionElems = AJS.$("option", parent),
                arr = [];
            AJS.$.each(availableOptionElems, function () {
                arr.push(instance._parseOption(this))
            });
            return arr
        }
        items.each(function (i) {
            var item = items.eq(i);
            if (item.is("optgroup")) {
                parseOptGroup(item)
            } else {
                if (item.is("option")) {
                    instance._parseOption(item)
                }
            }
        })
    },
    getSelectedDescriptors: function () {
        var descriptors = [];
        this.$element.find("option").each(function () {
            if (this.selected) {
                descriptors.push(AJS.$.data(this, "descriptor"))
            }
        });
        return descriptors
    },
    getAllDescriptors: function (showGroups) {
        var properties, descriptors = [];
        this.$element.children().each(function () {
            var descriptor, elem = AJS.$(this);
            if (elem.is("option")) {
                descriptors.push(elem.data("descriptor"))
            } else {
                if (elem.is("optgroup")) {
                    if (showGroups !== false) {
                        properties = AJS.copyObject(elem.data("descriptor").allProperties(), false);
                        properties.items = [];
                        descriptor = new AJS.GroupDescriptor(properties);
                        descriptors.push(descriptor)
                    }
                    elem.children("option").each(function () {
                        var elem = AJS.$(this);
                        if (showGroups !== false) {
                            descriptor.addItem(elem.data("descriptor"))
                        } else {
                            descriptors.push(elem.data("descriptor"))
                        }
                    })
                }
            }
        });
        return descriptors
    },
    clearUnSelected: function () {
        this.$element.find("option:not([selected])").remove()
    },
    getUnSelectedDescriptors: function (showGroups) {
        var descriptors = [],
            selectedValues = {},
            addedValues = {};

        function isValid(descriptor) {
            var descriptorVal = descriptor.value().toLowerCase();
            if (!selectedValues[descriptorVal] && (!addedValues[descriptorVal] || descriptor.allowDuplicate() !== false)) {
                addedValues[descriptorVal] = true;
                return true
            }
            return false
        }
        AJS.$.each(this.getSelectedDescriptors(), function (i, descriptor) {
            selectedValues[descriptor.value().toLowerCase()] = true
        });
        this.$element.children().each(function () {
            var descriptor, properties, elem = AJS.$(this);
            if (elem.is("option") && !this.selected) {
                descriptor = AJS.$.data(this, "descriptor");
                if (isValid(descriptor)) {
                    descriptors.push(descriptor)
                }
            } else {
                if (elem.is("optgroup")) {
                    if (showGroups !== false) {
                        properties = AJS.copyObject(elem.data("descriptor").allProperties(), false);
                        properties.items = [];
                        descriptor = new AJS.GroupDescriptor(properties);
                        descriptors.push(descriptor)
                    }
                    elem.find("option").each(function () {
                        if (this.selected) {
                            return
                        }
                        var itemDescriptor = AJS.$.data(this, "descriptor");
                        if (isValid(itemDescriptor)) {
                            if (showGroups !== false) {
                                descriptor.addItem(itemDescriptor)
                            } else {
                                descriptors.push(itemDescriptor)
                            }
                        }
                    })
                }
            }
        });
        return descriptors
    },
    _renders: {
        option: function (descriptor) {
            var option = new Option(descriptor.label(), descriptor.value());
            var $option = AJS.$(option);
            var iconUrl = descriptor.icon();
            option.title = descriptor.title();
            AJS.$.data(option, "descriptor", descriptor);
            descriptor.model($option);
            if (iconUrl) {
                $option.css("backgroundImage", "url(" + iconUrl + ")")
            }
            return $option
        },
        optgroup: function (descriptor) {
            var elem = AJS.$("<optgroup />").addClass(descriptor.styleClass()).attr("label", descriptor.label());
            descriptor.model(elem);
            elem.data("descriptor", descriptor);
            return elem
        }
    }
});
AJS.QueryableDropdownSelect = AJS.Control.extend({
    INVALID_KEYS: [JIRA.Keyboard.SpecialKey.TAB, JIRA.Keyboard.SpecialKey.ESC, JIRA.Keyboard.SpecialKey.SHIFT, JIRA.Keyboard.SpecialKey.RIGHT],
    init: function (options) {
        var instance = this;
        this._setOptions(options);
        this._queuedRequest = 0;
        this.suggestionsVisible = false;
        if (this.options.ajaxOptions.minQueryLength) {
            this.options.ajaxOptions.minQueryLength = parseInt(this.options.ajaxOptions.minQueryLength, 10)
        }
        this._createFurniture();
        this.dropdownController = AJS.InlineLayer.create({
            offsetTarget: this.$field,
            width: this.$field.innerWidth(),
            content: options.element
        });
        this.listController = new AJS.List({
            containerSelector: options.element,
            groupSelector: "ul.aui-list-section",
            itemSelector: "li",
            selectionHandler: function () {
                instance.$field.val(AJS.params.dotLoading).css("color", "#999");
                instance.hideSuggestions();
                return true
            }
        });
        this._assignEventsToFurniture();
        if (this.options.loadOnInit) {
            this.suggestionsDisabled = true;
            this._requestThenResetSuggestions()
        }
    },
    _getDefaultOptions: function () {
        return {
            id: "default",
            ajaxOptions: {
                data: {
                    query: ""
                },
                dataType: "json",
                minQueryLength: 0
            },
            keyInputPeriod: 75
        }
    },
    getAjaxOptions: function () {
        this.options.ajaxOptions.data.query = AJS.$.trim(this.$field.val());
        return AJS.copyObject(this.options.ajaxOptions)
    },
    issueRequest: function () {
        var instance = this,
            ajaxOptions = this.getAjaxOptions();
        ajaxOptions.complete = function (xhr, textStatus, smartAjaxResult) {
            instance.outstandingRequest = null;
            if (!instance.$container.is(":visible")) {
                return
            }
            if (smartAjaxResult.successful) {
                instance._handleServerSuccess(smartAjaxResult)
            } else {
                if (!smartAjaxResult.aborted) {
                    instance.hideSuggestions();
                    instance._handleServerError(smartAjaxResult)
                }
            }
        };
        this.outstandingRequest = JIRA.SmartAjax.makeRequest(ajaxOptions);
        AJS.$(this.outstandingRequest).throbber({
            target: this.$dropDownIcon,
            isLatentThreshold: 500
        })
    },
    _handleServerSuccess: function (smartAjaxResult) {
        if (this.options.loadOnInit || this.$field.val() == this.options.ajaxOptions.data.query) {
            this._handleSuggestionResponse(smartAjaxResult.data)
        }
    },
    _handleServerError: function (smartAjaxResult) {
        var errMsg = JIRA.SmartAjax.buildSimpleErrorContent(smartAjaxResult);
        alert(errMsg)
    },
    _createFurniture: function () {
        this.$container = this._render("container").insertBefore(this.options.element);
        this.$field = this._render("field").appendTo(this.$container);
        this.$dropDownIcon = this._render("dropdownAndLoadingIcon", this._hasDropdownButton()).appendTo(this.$container);
        this.$suggestionsContainer = this._render("suggestionsContainer")
    },
    _hasDropdownButton: function () {
        return this.options.showDropdownButton || this.options.ajaxOptions.minQueryLength === 0
    },
    _assignEventsToFurniture: function () {
        var instance = this;
        this.$field.preventBlurFromElements(instance.dropdownController.$layer, instance.$container);
        if (this._hasDropdownButton()) {
            this._assignEvents("ignoreBlurElement", this.$dropDownIcon);
            this._assignEvents("dropdownAndLoadingIcon", this.$dropDownIcon)
        }
        setTimeout(function () {
            instance._assignEvents("field", instance.$field);
            instance._assignEvents("keys", instance.$field)
        }, 15)
    },
    _useCachedRequest: function () {
        return !!(this.cachedList && !this.options.ajaxOptions.query)
    },
    _isValidRequest: function () {
        return this.options.ajaxOptions.query || (!this.cachedList && !this.outstandingRequest)
    },
    _requestThenResetSuggestions: function (ignoreBuffer) {
        var instance = this;
        this.listController._latestQuery = AJS.$.trim(this.$field.val());
        if (this._useCachedRequest()) {
            this._handleSuggestionResponse(this.cachedList)
        } else {
            if (this._isValidRequest()) {
                if (ignoreBuffer && this.outstandingRequest) {
                    this.outstandingRequest.abort();
                    this.outstandingRequest = null
                }
                clearTimeout(this._queuedRequest);
                if (!this.outstandingRequest) {
                    this.issueRequest()
                } else {
                    this._queuedRequest = setTimeout(function () {
                        instance._requestThenResetSuggestions(ignoreBuffer)
                    }, this.options.keyInputPeriod)
                }
            }
        }
    },
    _handleSuggestionResponse: function (data) {
        if (data !== this.cachedList) {
            if (this._formatResponse) {
                data = this._formatResponse(data)
            } else {
                if (this.options.ajaxOptions.formatResponse) {
                    data = this.options.ajaxOptions.formatResponse.call(this, data)
                }
            }
        }
        this.cachedList = data;
        this._setSuggestions(this.cachedList)
    },
    _setSuggestions: function (data) {
        if (this.suggestionsDisabled) {
            this.suggestionsDisabled = false;
            return
        }
        this.suggestionsVisible = true;
        if (data) {
            this.dropdownController.show();
            this.dropdownController.setWidth(this.$field.innerWidth());
            if (this.options.ajaxOptions.query) {
                this.listController.generateListFromJSON(data)
            } else {
                this.listController.generateListFromJSON(data, this.$field.val())
            }
            this.listController.enable()
        } else {
            this.hideSuggestions()
        }
    },
    _isValidInput: function (e) {
        return this.$field.is(":visible") && AJS.$.inArray(JIRA.Keyboard.specialKeyEntered(e), this.INVALID_KEYS) === -1
    },
    _handleCharacterInput: function (ignoreBuffer, ignoreQueryLength) {
        this.suggestionsDisabled = false;
        if (ignoreQueryLength || AJS.$.trim(this.$field.val()).length >= this.options.ajaxOptions.minQueryLength) {
            if (this.options.ajaxOptions.url) {
                this.$dropDownIcon.removeClass("noloading");
                this._requestThenResetSuggestions(ignoreBuffer)
            } else {
                this._setSuggestions(this.model.getUnSelectedDescriptors())
            }
        } else {
            this._setSuggestions()
        }
    },
    _handleDown: function (e) {
        if (!this.suggestionsVisible) {
            this._handleCharacterInput(true, true);
            e.stopPropagation()
        }
    },
    _rejectPendingRequests: function () {
        if (this.outstandingRequest) {
            this.outstandingRequest.abort()
        }
        clearTimeout(this._queuedRequest)
    },
    hideSuggestions: function () {
        if (!this.suggestionsVisible) {
            return
        }
        this._rejectPendingRequests();
        this.suggestionsVisible = false;
        this.$dropDownIcon.addClass("noloading");
        this.dropdownController.hide();
        this.listController.disable()
    },
    _deactivate: function () {
        this.hideSuggestions()
    },
    _handleEscape: function (e) {
        if (this.suggestionsVisible) {
            e.stopPropagation();
            if (e.type === "keyup") {
                this.hideSuggestions()
            }
        }
    },
    keys: {
        down: function (e) {
            if (this._hasDropdownButton()) {
                this._handleDown(e)
            }
        },
        up: function (e) {
            e.preventDefault()
        },
        "return": function (e) {
            e.preventDefault()
        },
        onEdit: function (e, character) {
            var instance = this;
            this.$field.one("keyup", function () {
                instance._handleCharacterInput()
            })
        }
    },
    _events: {
        dropdownAndLoadingIcon: {
            click: function (e) {
                this.$field.focus();
                if (this.suggestionsVisible) {
                    this.hideSuggestions()
                } else {
                    this._handleDown(e)
                }
                e.stopPropagation()
            }
        },
        field: {
            blur: function () {
                this._deactivate()
            },
            click: function (e) {
                e.stopPropagation()
            },
            keyup: function (e) {
                if (e.keyCode === JIRA.Keyboard.SpecialKey.toKeyCode(JIRA.Keyboard.SpecialKey.ESC)) {
                    this._handleEscape(e)
                }
            }
        },
        keys: {
            "keydown keypress": function (e) {
                this._handleKeyEvent(e)
            }
        },
        ignoreBlurElement: {
            mousedown: function (e) {
                if (e.target !== this.$field[0]) {
                    this.ignoreBlurEvent = true;
                    e.preventDefault()
                }
            }
        }
    },
    _renders: {
        field: function () {
            return AJS.$("<input class='text' type='text' autocomplete='off' />")
        },
        container: function () {
            return AJS.$("<div class='queryable-select' id='" + this.options.id + "-queryable-container' />")
        },
        dropdownAndLoadingIcon: function (showDropdown) {
            var $element = AJS.$('<span class="icon noloading"><span>More</span></span>');
            if (showDropdown) {
                $element.addClass("drop-menu")
            }
            return $element
        },
        suggestionsContainer: function () {
            return AJS.$("<div class='aui-list' id='" + this.options.id + "' tabindex='-1'></div>")
        }
    }
});
AJS.namespace("AJS.QueryableDropdown", null, AJS.QueryableDropdownSelect);
AJS.MultiSelect = AJS.QueryableDropdownSelect.extend({
    init: function (options) {
        var instance = this;
        if (this._setOptions(options) === this.INVALID) {
            return this.INVALID
        }
        AJS.$(this.options.element).hide();
        if (this.options.disabled) {
            this._createFurniture(true);
            return this
        }
        this.model = new AJS.SelectModel({
            element: this.options.element,
            removeOnUnSelect: this.options.removeOnUnSelect
        });
        this._createFurniture();
        this.dropdownController = AJS.InlineLayer.create({
            alignment: AJS.LEFT,
            offsetTarget: this.$field,
            content: AJS.$(".aui-list", this.$container)
        });
        this.listController = new AJS.List({
            containerSelector: AJS.$(".aui-list", this.$container),
            groupSelector: "ul.aui-list-section",
            itemSelector: "li",
            matchingStrategy: this.options.matchingStrategy,
            selectionHandler: function (e) {
                instance._selectionHandler(this.getFocused(), e);
                return false
            }
        });
        this._assignEventsToFurniture();
        this._setTextareaDimThresholds();
        this.lozengeGroup = new AJS.MultiSelect.LozengeGroup();
        this._assignEvents("lozengeGroup", this.lozengeGroup);
        this._restoreSelectedOptions();
        return this
    },
    _getDefaultOptions: function () {
        return AJS.$.extend(true, this._super(), {
            minRoomForText: 50,
            errorMessage: AJS.params.multiselectGenericError,
            ajaxOptions: {
                minQueryLength: 1
            },
            showDropdownButton: true
        })
    },
    _createFurniture: function (disabled) {
        var id = this.model.$element.attr("id");
        if (this.model.$element.prev().hasClass("ajs-multi-select-placeholder")) {
            this.model.$element.prev().remove()
        }
        if (disabled) {
            this.model.$element.replaceWith(this._render("disableSelectField", id))
        } else {
            this.$container = this._render("container", id);
            this.$field = this._render("field", id).appendTo(this.$container);
            this.$container.append(this._render("suggestionsContainer", id));
            this.$container.insertBefore(this.model.$element);
            this.$dropDownIcon = this._render("dropdownAndLoadingIcon", this._hasDropdownButton()).appendTo(this.$container);
            this.$errorMessage = this._render("errorMessage");
            this.$selectedItemsWrapper = this._render("selectedItemsWrapper").appendTo(this.$container);
            this.$selectedItemsContainer = this._render("selectedItemsContainer").appendTo(this.$selectedItemsWrapper)
        }
    },
    _assignEventsToFurniture: function () {
        this._super();
        this._assignEvents("body", document);
        this._assignEvents("selectedItemsContainer", this.$selectedItemsContainer)
    },
    _getUserInputValue: function () {
        return this.options.uppercaseUserEnteredOnSelect ? this.$field.val().toUpperCase() : this.$field.val()
    },
    _handleUserInputOption: function () {
        var groupDescriptor;
        if (!this.hasUserInputtedOption() || this.$field.val().length === 0) {
            return
        }
        groupDescriptor = new AJS.GroupDescriptor({
            type: "optgroup",
            label: "user inputted option",
            weight: 9999,
            showLabel: false,
            replace: true
        });
        groupDescriptor.addItem(new AJS.ItemDescriptor({
            value: this._getUserInputValue(),
            label: this.$field.val(),
            labelSuffix: " (" + this.options.userEnteredOptionsMsg + ")",
            title: this.$field.val(),
            allowDuplicate: false
        }));
        this.model.appendOptionsFromJSON([groupDescriptor])
    },
    hasUserInputtedOption: function () {
        return this.options.userEnteredOptionsMsg
    },
    _handleSuggestionResponse: function (data) {
        if (this.options.ajaxOptions.query) {
            this.model.clearUnSelected()
        }
        this._handleUserInputOption();
        this._super(data)
    },
    _setSuggestions: function (data) {
        if (data) {
            this.model.appendOptionsFromJSON(data);
            this._super(this.model.getUnSelectedDescriptors())
        } else {
            this.hideSuggestions()
        }
    },
    removeItem: function (descriptor) {
        this.model.setUnSelected(descriptor);
        this.updateItemsIndent()
    },
    _restoreSelectedOptions: function () {
        var instance = this;
        AJS.$.each(this.model.getSelectedDescriptors(), function () {
            instance._addItem(this)
        });
        this.updateItemsIndent()
    },
    _shouldEnableLozengeGroup: function () {
        return this.lozengeGroup.items.length > 0 && this.lozengeGroup.index < 0 && (this.$field.val().length === 0 || this.getCaret(this.$field[0]) === 0)
    },
    _handleBackSpace: function () {
        var instance = this;
        if (this._shouldEnableLozengeGroup()) {
            setTimeout(function () {
                instance.lozengeGroup.shiftFocus(-1)
            }, 0)
        } else {
            this.$field.one("keyup", function () {
                instance._handleCharacterInput()
            })
        }
    },
    _handleDelete: function () {
        if (AJS.$.trim(this.$field.val()) !== "") {
            var instance = this;
            this.$field.one("keyup", function () {
                instance._handleCharacterInput()
            })
        }
    },
    _handleLeft: function () {
        if (this._shouldEnableLozengeGroup()) {
            var instance = this;
            setTimeout(function () {
                instance.lozengeGroup.shiftFocus(-1)
            }, 0)
        }
    },
    _handlePaste: function () {
        this.$field.val(AJS.$.trim(this.$field.val()).replace(/\s+/g, " "));
        this._handleCharacterInput()
    },
    _setTextareaDimThresholds: function () {
        this.maxWidth = this.options.maxWidth || this.$container.attr("scrollWidth");
        this.minWidth = this.options.minWidth || this.$container.attr("scrollWidth");
        if (this.minWidth > this.maxWidth) {
            this.minWidth = this.maxWidth
        }
    },
    updateItemsIndent: function () {
        var inputIndent = this._getInputIndent();
        this.$field.css({
            paddingTop: inputIndent.top,
            paddingLeft: inputIndent.left,
            width: 290
        });
        this.$field.css({
            width: this.$container.width() - inputIndent.left - 21
        });
        if (this.currentTopOffset && this.currentTopOffset !== inputIndent.top) {
            this.$container.trigger("multiSelectHeightUpdated", [this])
        }
        if (AJS.$.browser.msie) {
            this.$field.val(this.$field.val() + " ");
            this.$field.val(this.$field.val().replace(/\s$/, ""))
        }
        this.currentTopOffset = inputIndent.top
    },
    _isItemPresent: function (descriptor) {
        var duplicate = false;
        var label = descriptor.label();
        AJS.$.each(this.lozengeGroup.items, function () {
            if (this.options.label === label) {
                duplicate = true;
                return false
            }
        });
        return duplicate
    },
    _addItem: function (descriptor) {
        if (descriptor instanceof AJS.ItemDescriptor) {
            descriptor = AJS.copyObject(descriptor.allProperties(), false)
        }
        descriptor.value = AJS.$.trim(descriptor.value);
        descriptor.label = AJS.$.trim(descriptor[this.options.itemAttrDisplayed]) || descriptor.value;
        descriptor.title = AJS.$.trim(descriptor.title) || descriptor.label;
        descriptor = new AJS.ItemDescriptor(descriptor);
        if (this._isItemPresent(descriptor)) {
            return
        }
        var lozenge = new AJS.MultiSelect.Lozenge({
            label: descriptor.label(),
            title: descriptor.title(),
            container: this.$selectedItemsContainer
        });
        this.lozengeGroup.addItem(lozenge);
        this._assignEvents("lozenge", lozenge);
        this.model.setSelected(descriptor);
        this.updateItemsIndent();
        this.dropdownController.setPosition()
    },
    _addMultipleItems: function (items, removeOnUnSelect) {
        var instance = this;
        AJS.$.each(items, function (i, descriptor) {
            if (removeOnUnSelect) {
                descriptor.removeOnUnSelect = true
            }
            instance._addItem(descriptor)
        })
    },
    _getTargetItemContainerWidth: function () {
        var lozenges = this.lozengeGroup.items,
            width = parseInt(this.$selectedItemsContainer.css("paddingLeft"), 10) + parseInt(this.$selectedItemsContainer.css("paddingRight"), 10);
        for (var i = lozenges.length - 1; i >= 0; i--) {
            var $item = lozenges[i].$lozenge;
            width = width + $item.outerWidth() + parseInt($item.css("marginLeft"), 10) + parseInt($item.css("marginRight"), 10)
        }
        return width
    },
    _getInputIndent: function () {
        var top, left, iconArea = 22,
            paddingLeft = 2,
            paddingTop = 4,
            indent = {
                top: paddingTop,
                left: paddingLeft
            },
            lastLozengeIndex = this.lozengeGroup.items.length - 1,
            $last;
        if (lastLozengeIndex >= 0) {
            $last = this.lozengeGroup.items[lastLozengeIndex].$lozenge;
            top = $last.attr("offsetTop");
            left = $last.attr("offsetLeft") + $last.outerWidth();
            if (left > this.$container.width() - iconArea - this.options.minRoomForText) {
                top += $last.attr("offsetHeight");
                left = 0
            }
            indent.top += top;
            indent.left += left
        }
        return indent
    },
    _selectionHandler: function (selected, e) {
        var instance = this;
        selected.each(function () {
            instance._addItem(AJS.$.data(this, "descriptor"))
        });
        this.$field.val("").focus().scrollIntoView({
            margin: 20
        });
        this.hideSuggestions();
        this.hideErrorMessage();
        this.model.$element.trigger("change");
        e.preventDefault()
    },
    isValidItem: function (itemValue) {
        var suggestedItemDescriptor = this.listController.getFocused().data("descriptor");
        if (!suggestedItemDescriptor) {
            return false
        }
        itemValue = itemValue.toLowerCase();
        return itemValue === AJS.$.trim(suggestedItemDescriptor.label.toLowerCase()) || itemValue === AJS.$.trim(suggestedItemDescriptor.value.toLowerCase())
    },
    showErrorMessage: function (value) {
        this.$errorMessage.text(AJS.format(this.options.errorMessage, value || this.$field.val()));
        var $container = this.$container.parent(".field-group");
        if ($container.length === 0) {
            $container = this.$container.parent(".frother-control-renderer");
            this.$errorMessage.prependTo($container)
        } else {
            this.$errorMessage.appendTo($container)
        }
    },
    hideErrorMessage: function () {
        this.$errorMessage.remove()
    },
    handleFreeInput: function () {
        var value = AJS.$.trim(this.$field.val()),
            descriptor;
        if (value) {
            descriptor = this.model.getDescriptor(value);
            if (descriptor) {
                this._addItem(descriptor);
                this.model.$element.trigger("change")
            } else {
                this.showErrorMessage(value);
                return
            }
        }
        this.hideErrorMessage();
        this.$field.val("")
    },
    submitForm: function () {
        if (this.$field.val().length === 0 && !this.suggestionsVisible) {
            AJS.$(this.$field[0].form).submit()
        }
    },
    _deactivate: function () {
        this.handleFreeInput();
        this.lozengeGroup.trigger("blur");
        this.hideSuggestions()
    },
    keys: {
        left: function () {
            this._handleLeft()
        },
        backspace: function () {
            this._handleBackSpace()
        },
        del: function () {
            this._handleDelete()
        },
        "return": function (e) {
            this.submitForm();
            e.preventDefault()
        }
    },
    _events: {
        body: {
            tabSelect: function () {
                if (this.$field.is(":visible")) {
                    this._setTextareaDimThresholds();
                    this.updateItemsIndent()
                }
            },
            bulkTabSelect: function () {
                if (this.$field.is(":visible")) {
                    this._setTextareaDimThresholds();
                    this.updateItemsIndent()
                }
            }
        },
        field: {
            paste: function () {
                setTimeout(AJS.$.proxy(this, "_handlePaste"), 0)
            },
            "keydown keypress": function (event) {
                if (this.lozengeGroup.index >= 0) {
                    if (JIRA.Keyboard.SpecialKey.fromKeyCode(event.keyCode) in this.lozengeGroup.keys) {
                        event.preventDefault()
                    } else {
                        if (JIRA.Keyboard.SpecialKey.fromKeyCode(event.keyCode) === "return") {
                            this.submitForm();
                            event.preventDefault()
                        } else {
                            var instance = this;
                            this.$field.one("keyup", function () {
                                instance._handleCharacterInput()
                            });
                            this.lozengeGroup.trigger("blur")
                        }
                    }
                }
            },
            click: function () {
                this.lozengeGroup.trigger("blur");
                this.$field.focus()
            }
        },
        lozengeGroup: {
            focus: function () {
                this.$field.focus();
                this.hideSuggestions();
                this._unassignEvents("keys", this.$field)
            },
            blur: function () {
                this._assignEvents("keys", this.$field);
                if (this.$field.val()) {
                    this._handleCharacterInput()
                }
            }
        },
        lozenge: {
            remove: function (event) {
                this.removeItem(this.model.getDescriptor(event.target.options.label))
            }
        },
        selectedItemsContainer: {
            click: function (event) {
                if (event.target === event.currentTarget) {
                    this.lozengeGroup.trigger("blur");
                    this.$field.focus()
                }
            }
        }
    },
    _renders: {
        errorMessage: function () {
            return AJS.$('<div class="error" />')
        },
        selectedItemsWrapper: function () {
            return AJS.$('<div class="representation"></div>')
        },
        selectedItemsContainer: function () {
            return AJS.$('<ul class="items" />')
        },
        field: function (idPrefix) {
            return AJS.$('<textarea autocomplete="off" id="' + idPrefix + '-textarea" class="aui-field" wrap="off"></textarea>')
        },
        disableSelectField: function (id) {
            return AJS.$("<input type='text' class='long-field' name='" + id + "' id='" + id + "' />")
        },
        container: function (idPrefix) {
            return AJS.$('<div class="multi-select" id="' + idPrefix + '-multi-select">')
        },
        suggestionsContainer: function (idPrefix) {
            return AJS.$('<div class="aui-list" id="' + idPrefix + '-suggestions" tabindex="-1"></div>')
        }
    }
});
AJS.MultiSelect.Lozenge = AJS.Control.extend({
    init: function (options) {
        this._setOptions(options);
        this.$lozenge = this._render("lozenge");
        this.$removeButton = this._render("removeButton");
        this._assignEvents("instance", this);
        this._assignEvents("lozenge", this.$lozenge);
        this._assignEvents("removeButton", this.$removeButton);
        this.$removeButton.appendTo(this.$lozenge);
        this.$lozenge.appendTo(this.options.container)
    },
    _getDefaultOptions: function () {
        return {
            label: null,
            title: null,
            container: null,
            focusClass: "focused"
        }
    },
    _renders: {
        "lozenge": function () {
            var label = AJS.escapeHTML(this.options.label);
            var title = AJS.escapeHTML(this.options.title) || "";
            return AJS.$('<li class="item-row" title="' + title + '"><button type="button" tabindex="-1" class="value-item"><span><span class="value-text">' + label + "</span></span></button></li>")
        },
        "removeButton": function () {
            return AJS.$('<em class="item-delete" title="' + AJS.params.removeItem + '"></em>')
        }
    },
    _events: {
        "instance": {
            "focus": function () {
                this.$lozenge.addClass(this.options.focusClass)
            },
            "blur": function () {
                this.$lozenge.removeClass(this.options.focusClass)
            },
            "remove": function () {
                this.$lozenge.remove()
            }
        },
        "lozenge": {
            "click": function () {
                this.trigger("focus")
            }
        },
        "removeButton": {
            "click": function () {
                this.trigger("remove")
            }
        }
    }
});
AJS.MultiSelect.LozengeGroup = AJS.Group.extend({
    keys: {
        "left": function () {
            if (this.index > 0) {
                this.shiftFocus(-1)
            }
        },
        "right": function () {
            if (this.index === this.items.length - 1) {
                this.items[this.index].trigger("blur")
            } else {
                this.shiftFocus(1)
            }
        },
        "backspace": function () {
            var index = this.index;
            if (index > 0) {
                this.shiftFocus(-1)
            } else {
                if (this.items.length > 1) {
                    this.shiftFocus(1)
                }
            }
            this.items[index].trigger("remove")
        },
        "del": function () {
            var index = this.index;
            if (index + 1 < this.items.length) {
                this.shiftFocus(1)
            }
            this.items[index].trigger("remove")
        }
    }
});
MultiSelect = AJS.MultiSelect.extend({
});
AJS.namespace("multiselect", null, MultiSelect);
AjaxMultiSelect = AJS.MultiSelect.extend({
    _getDefaultOptions: function(){
        return AJS.$.extend(true, this._super(), {
            removeOnUnSelect: true,
            userEnteredOptionsMsg: AJS.params.labelNew
	})
    },
    isValidItem: function (itemValue) {
        return !/\s/.test(itemValue)
    },
    _handleSuggestionResponse: function (data) {
	if (data && data.token) {
	    if (AJS.$.trim(this.$field.val()) !== data.token) {
		return
            }
	}
	this._super(data)
    },
    _formatResponse: function (data) {
	var optgroup = new AJS.GroupDescriptor({
	    label: "请选择",
	    type: "optgroup",
	    weight: 1,
	    styleClass: "labels-suggested"
	});
        if (data && data.result.data) {
	    AJS.$.each(data.result.data, function () {
	    	optgroup.addItem(new AJS.ItemDescriptor({
		    value: this.label,
		    label: this.label,
		    html: this.html
		}))
	    })
	}
        return [optgroup]
    }
});
AJS.namespace("ajaxmultiselect", null, AjaxMultiSelect);
JIRA.IssuePicker = AJS.MultiSelect.extend({
    _formatResponse: function (response) {
        var ret = [],
            canonicalBaseUrl = (function () {
                var uri = parseUri(window.location);
                return uri.protocol + "://" + uri.authority
            })();
        if (response && response.sections) {
            AJS.$(response.sections).each(function (i, section) {
                var groupDescriptor = new AJS.GroupDescriptor({
                    weight: i,
                    label: section.label,
                    description: section.sub
                });
                if (section.issues && section.issues.length > 0) {
                    AJS.$(section.issues).each(function () {
                        groupDescriptor.addItem(new AJS.ItemDescriptor({
                            value: this.key,
                            label: this.key + " - " + this.summaryText,
                            icon: this.img ? canonicalBaseUrl + contextPath + this.img : null,
                            html: this.keyHtml + " - " + this.summary
                        }))
                    })
                }
                ret.push(groupDescriptor)
            })
        }
        return ret
    },
    getAjaxOptions: function () {
        var ajaxOptions = this._super();
        if (this.$field.val().length === 0) {
            delete ajaxOptions.data.currentJQL
        }
        return ajaxOptions
    },
    hasUserInputtedOption: function () {
        return this.$field.val().length !== 0
    },
    _launchPopup: function () {
        function getWithDefault(value, def) {
            if (typeof value == "undefined" || value == null) {
                return def
            } else {
                return value
            }
        }
        var url, urlParam, vWinUsers, options, instance = this;
        JIRA.IssuePicker.callback = function (items) {
            if (typeof items === "string") {
                items = JSON.parse(items)
            }
            instance._addMultipleItems(items, true);
            instance.$field.focus()
        };
        options = this.options.ajaxOptions.data;
        url = contextPath + "/secure/popups/IssuePicker.jspa?";
        urlParam = {
            singleSelectOnly: "false",
            currentIssue: options.currentIssueKey || "",
            showSubTasks: getWithDefault(options.showSubTasks, false),
            showSubTasksParent: getWithDefault(options.showSubTaskParent, false)
        };
        if (options.currentProjectId) {
            urlParam["currentProjectId"] = options.currentProjectId
        }
        url += AJS.$.param(urlParam);
        vWinUsers = window.open(url, "IssueSelectorPopup", "status=no,resizable=yes,top=100,left=200,width=" + this.options.popupWidth + ",height=" + this.options.popupHeight + ",scrollbars=yes,resizable");
        vWinUsers.opener = self;
        vWinUsers.focus()
    },
    _createFurniture: function (disabled) {
        var $popupLink;
        this._super(disabled);
        $popupLink = this._render("popupLink");
        this._assignEvents("popupLink", $popupLink);
        this.$container.addClass("hasIcon");
        $popupLink.appendTo(this.$container)
    },
    handleFreeInput: function () {
        var values = this.$field.val().toUpperCase().match(/\S+/g);
        if (values) {
            this._addMultipleItems(jQuery.map(values, function (value) {
                return {
                    value: value,
                    label: value
                }
            }))
        }
        this.$field.val("")
    },
    _events: {
        popupLink: {
            click: function (e) {
                this._launchPopup();
                e.preventDefault()
            }
        }
    },
    _renders: {
        popupLink: function () {
            return AJS.$("<a class='issue-picker-popup' />").attr({
                href: "#",
                title: this.options.popupLinkMessage
            }).text("" + this.options.popupLinkMessage + "")
        }
    }
});
AJS.namespace("jira.issuepicker", null, JIRA.IssuePicker);
AJS.namespace("AJS.IssuePicker", null, JIRA.IssuePicker);
JIRA.LabelPicker = AJS.MultiSelect.extend({
    _getDefaultOptions: function () {
        return AJS.$.extend(true, this._super(), {
            ajaxOptions: {
                url: contextPath + "/includes/js/ajs/layer/labeldata.js",
                query: true
            },
            removeOnUnSelect: true,
            userEnteredOptionsMsg: AJS.params.labelNew
        })
    },
    isValidItem: function (itemValue) {
        return !/\s/.test(itemValue)
    },
    _handleSuggestionResponse: function (data) {
        if (data && data.token) {
            if (AJS.$.trim(this.$field.val()) !== data.token) {
                return
            }
        }
        this._super(data)
    },
    _handleDown: function (e) {
        if (!this.suggestionsVisible) {
            this._requestThenResetSuggestions();
            e.stopPropagation()
        }
    },
    _handleSpace: function () {
        if (AJS.$.trim(this.$field.val()) !== "") {
            if (this.handleFreeInput()) {
                this.hideSuggestions()
            }
        }
    },
    keys: {
        space: function (e) {
            this._handleSpace();
            e.preventDefault()
        }
    },
    _formatResponse: function (data) {
        var optgroup = new AJS.GroupDescriptor({
            label: AJS.params.frotherSuggestions,
            type: "optgroup",
            weight: 1,
            styleClass: "labels-suggested"
        });
        if (data && data.suggestions) {
            AJS.$.each(data.suggestions, function () {
                optgroup.addItem(new AJS.ItemDescriptor({
                    value: this.label,
                    label: this.label,
                    html: this.html
                }))
            })
        }
        return [optgroup]
    },
    handleFreeInput: function () {
        var values = AJS.$.trim(this.$field.val()).match(/\S+/g);
        if (values) {
            for (var value, i = 0; value = values[i]; i++) {
                this._addItem({
                    value: value,
                    label: value
                })
            }
            this.model.$element.trigger("change")
        }
        this.$field.val("")
    }
});
AJS.namespace("AJS.LabelPicker", null, JIRA.LabelPicker);
JIRA.Dialog = AJS.Control.extend({
    _getDefaultOptions: function () {
        return {
            height: "auto",
            cached: false,
            widthClass: "medium",
            ajaxOptions: {
                data: {
                    inline: true,
                    decorator: "dialog"
                }
            }
        }
    },
    init: function (options) {
        if (typeof options === "string" || options instanceof jQuery) {
            options = {
                trigger: options
            }
        } else {
            if (options && options.width) {
                options.widthClass = "custom"
            }
        }
        this.options = jQuery.extend(true, this._getDefaultOptions(), options);
        this.options.width = JIRA.Dialog.WIDTH_PRESETS[this.options.widthClass] || options.width;
        if (typeof this.options.content === "function") {
            this.options.type = "builder"
        } else {
            if (this.options.content instanceof jQuery || (typeof this.options.content === "object" && this.options.nodeName)) {
                this.options.type = "element"
            } else {
                if (!this.options.type && !this.options.content || (typeof this.options.content === "object" && this.options.content.url)) {
                    this.options.type = "ajax"
                }
            }
        }
        if (this.options.trigger) {
            this._assignEvents("trigger", this.options.trigger)
        }
        this.onContentReadyCallbacks = [];
        this._assignEvents("container", document)
    },
    _runContentReadyCallbacks: function () {
        var that = this;
        AJS.$.each(this.onContentReadyCallbacks, function () {
            this.call(that)
        })
    },
    _setContent: function (content, decorate) {
        if (!content) {
            this._contentRetrievers[this.options.type].call(this, this._setContent)
        } else {
            if (JIRA.Dialog.current === this) {
                var $popup = this.get$popup();
                this.$content = content;
                this.get$popupContent().html(content);
                $popup.addClass("popup-width-" + this.options.widthClass);
                $popup.css({
                    marginLeft: -9999
                }).show();
                if (decorate !== false) {
                    if (this.decorateContent) {
                        this.decorateContent()
                    }
                    AJS.$(document).trigger("dialogContentReady", [this]);
                    this._runContentReadyCallbacks()
                }
                this._positionInCenter();
                if (decorate !== false) {
                    if (AJS.$.isFunction(this.options.onContentRefresh)) {
                        this.options.onContentRefresh.call(this)
                    }
                }
                AJS.$(".aui-dialog-open").addClass("aui-dialog-content-ready")
            } else {
                if (this.options.cached === false) {
                    delete this.$content
                }
            }
        }
    },
    _handleInitialDoneResponse: function (data, xhr, smartAjaxResult) {},
    _getRequestOptions: function () {
        var options = {};
        if (this._getAjaxOptionsObject() === false) {
            return false
        }
        options = AJS.$.extend(true, options, this._getAjaxOptionsObject());
        if (!options.url && this.$activeTrigger) {
            options.url = this.$activeTrigger.attr("href")
        }
        return options
    },
    _getAjaxOptionsObject: function () {
        var ajaxOpts = this.options.ajaxOptions;
        if (AJS.$.isFunction(ajaxOpts)) {
            return ajaxOpts.call(this)
        } else {
            return ajaxOpts
        }
    },
    _contentRetrievers: {
        "element": function (callback) {
            if (!this.$content) {
                this.$content = jQuery(this.options.content).clone(true)
            }
            callback.call(this, this.$content)
        },
        "builder": function (callback) {
            this.$content = this.options.content.call(this);
            callback.call(this, this.$content)
        },
        "ajax": function (callback) {
            var instance = this,
                ajaxOptions;
            if (!this.$content) {
                ajaxOptions = this._getRequestOptions();
                this._showloadingIndicator();
                this.serverIsDone = false;
                ajaxOptions.complete = function (xhr, textStatus, smartAjaxResult) {
                    if (smartAjaxResult.successful) {
                        var instructions = instance._detectRedirectInstructions(xhr);
                        instance.serverIsDone = instructions.serverIsDone;
                        if (instructions.redirectUrl) {
                            instance._performRedirect(instructions.redirectUrl)
                        } else {
                            if (ajaxOptions.dataType && ajaxOptions.dataType.toLowerCase() === "json" && instance._buildContentFromJSON) {
                                instance.$content = instance._buildContentFromJSON(smartAjaxResult.data)
                            } else {
                                instance.$content = smartAjaxResult.data
                            }
                            if (instance.serverIsDone) {
                                instance._handleInitialDoneResponse(smartAjaxResult.data, xhr, smartAjaxResult)
                            } else {
                                instance._hideloadingIndicator();
                                callback.call(instance, instance.$content)
                            }
                        }
                    } else {
                        instance._hideloadingIndicator();
                        var errorContent = JIRA.SmartAjax.buildDialogErrorContent(smartAjaxResult);
                        callback.call(instance, errorContent)
                    }
                };
                JIRA.SmartAjax.makeRequest(ajaxOptions)
            }
        }
    },
    _detectRedirectInstructions: function (xhr) {
        var instructions = {
            serverIsDone: false,
            redirectUrl: ""
        };
        var doneHeader = xhr.getResponseHeader("X-Atlassian-Dialog-Control");
        if (doneHeader) {
            instructions.serverIsDone = true;
            var idx = doneHeader.indexOf("redirect:");
            if (idx == 0) {
                instructions.redirectUrl = doneHeader.substr("redirect:".length)
            }
        }
        return instructions
    },
    _performRedirect: function (url) {
        AJS.reloadViaWindowLocation(url)
    },
    _renders: {
        popupHeading: function () {
            return jQuery("<h2 />").addClass("aui-popup-heading")
        },
        popupContent: function () {
            return jQuery("<div />").addClass("aui-popup-content")
        },
        popup: function () {
            return jQuery("<div />").attr("id", this.options.id || "").addClass("aui-popup").hide()
        },
        loadingIndicator: function () {
            return jQuery("<div />").addClass("aui-loading")
        }
    },
    _events: {
        "trigger": {
            click: function (e, item) {
                this.$activeTrigger = item;
                this.show();
                e.preventDefault()
            }
        },
        "container": {
            "keydown": function (e) {
                function calendarClosingBy(e) {
                    if (window._dynarch_popupCalendar && !window._dynarch_popupCalendar.hidden) {
                        return true
                    } else {
                        if (e.calendarClosed) {
                            return true
                        } else {
                            if (e.originalEvent && e.originalEvent.calendarClosed) {
                                return true
                            }
                        }
                    }
                    return false
                }
                if (e.which === jQuery.ui.keyCode.ESCAPE && !AJS.InlineLayer.current && !JIRA.Dropdown.current && !calendarClosingBy(e)) {
                    this.handleCancel()
                }
            }
        }
    },
    handleCancel: function () {
        this.hide()
    },
    _get$loadingIndicator: function () {
        if (!JIRA.Dialog.$loadingIndicator) {
            JIRA.Dialog.$loadingIndicator = this._render("loadingIndicator").css("zIndex", 9999).appendTo("body")
        }
        return JIRA.Dialog.$loadingIndicator
    },
    _showloadingIndicator: function () {
        var instance = this,
            heightOfSprite = 440,
            currentOffsetOfSprite = 0;
        clearInterval(this.loadingTimer);
        this._get$loadingIndicator().show();
        this.loadingTimer = window.setInterval(function () {
            if (currentOffsetOfSprite === heightOfSprite) {
                currentOffsetOfSprite = 0
            }
            currentOffsetOfSprite = currentOffsetOfSprite + 40;
            instance._get$loadingIndicator().css("backgroundPosition", "0 -" + currentOffsetOfSprite + "px")
        }, 50)
    },
    _hideloadingIndicator: function () {
        clearInterval(this.loadingTimer);
        this._get$loadingIndicator().hide()
    },
    _positionInCenter: function () {
        var $window = AJS.$(window),
            $popup = this.get$popup(),
            $heading = $popup.find(".aui-popup-heading"),
            $container = $popup.find(".content-body"),
            $footer = $popup.find(".content-footer");
        var cushion = 40;
        var windowHeight = $window.height();
        if (typeof this.options.width === "number") {
            $popup.width(this.options.width)
        }
        $popup.css({
            marginLeft: -$popup.outerWidth() / 2,
            marginTop: Math.max(-$popup.outerHeight() / 2, cushion - windowHeight / 2)
        });
        var top = 0;
        var el = $popup[0];
        while (el) {
            top += el.offsetTop;
            el = el.offsetParent
        }
        var popupMaxHeight = windowHeight - top - cushion;
        var padding = parseInt($container.css("padding-top"), 10) + parseInt($container.css("padding-bottom"), 10);
        $container.css("max-height", popupMaxHeight - $heading.outerHeight() - $footer.outerHeight() - padding)
    },
    get$popup: function () {
        if (!this.$popup) {
            this.$popup = this._render("popup").appendTo("body");
            if (this._supportsBoxShadow()) {
                this.$popup.addClass("box-shadow")
            }
        }
        return this.$popup
    },
    get$popupContent: function () {
        if (!this.$popupContent) {
            this.$popupContent = this._render("popupContent").appendTo(this.get$popup())
        }
        return this.$popupContent
    },
    get$popupHeading: function () {
        if (!this.$popupHeading) {
            this.$popupHeading = this._render("popupHeading").prependTo(this.get$popup())
        }
        return this.$popupHeading
    },
    _watchTab: function (e) {
        var $dialog_selectable, $first_selectable, $last_selectable;
        if (AJS.$(e.target).parents(this.get$popupContent()).length > 0) {
            if (AJS.$.browser.safari) {
                $dialog_selectable = AJS.$(":input:visible:enabled, :checkbox:visible:enabled, :radio:visible:enabled", ".aui-popup.aui-dialog-open")
            } else {
                $dialog_selectable = AJS.$("a:visible, :input:visible:enabled, :checkbox:visible:enabled, :radio:visible:enabled", ".aui-popup.aui-dialog-open")
            }
            $first_selectable = $dialog_selectable.first();
            $last_selectable = $dialog_selectable.last();
            if ((e.target == $first_selectable[0] && e.shiftKey) || (e.target == $last_selectable[0] && !e.shiftKey)) {
                if (e.shiftKey) {
                    $last_selectable.focus()
                } else {
                    $first_selectable.focus()
                }
                e.preventDefault()
            }
        }
    },
    show: function () {
        var myEvent = new AJS.$.Event("beforeShow");
        if (JIRA.Dialog.current === this) {
            return false
        }
        AJS.$(this).trigger(myEvent);
        if (myEvent.result === false) {
            return false
        }
        if (AJS.InlineLayer.current) {
            AJS.InlineLayer.current.hide()
        }
        if (AJS.dropDown.current) {
            AJS.dropDown.current.hide()
        }
        if (JIRA.Dialog.current) {
            JIRA.Dialog.current.hide(false)
        } else {
            AJS.dim(false)
        }
        JIRA.Dialog.current = this;
        var $popup = this.get$popup().addClass("aui-dialog-open");
        if (this.options.type !== "blank" && !this.$content) {
            this._setContent()
        } else {
            $popup.show();
            this._positionInCenter()
        }
        this.tabWatcher = function (e) {
            if (e.keyCode == 9) {
                JIRA.Dialog.current._watchTab(e)
            }
        };
        AJS.$(document).bind("keydown", this.tabWatcher);
        AJS.disableKeyboardScrolling()
    },
    destroy: function () {
        this.$popup.remove();
        delete this.$popup;
        delete this.$popupContent;
        delete this.$popupHeading;
        delete this.$content
    },
    hide: function (undim) {
        if (JIRA.Dialog.current !== this) {
            return false
        }
        var atlToken = AJS.$(".aui-dialog-open  input[name=atl_token]").attr("value");
        if (atlToken !== undefined) {
            JIRA.XSRF.updateTokenOnPage(atlToken)
        }
        if (this.options.cached === false) {
            this.destroy()
        }
        if (undim !== false) {
            AJS.undim()
        }
        this.get$popup().removeClass("aui-dialog-open").removeClass("aui-dialog-content-ready").hide();
        JIRA.Dialog.current = null;
        AJS.$(document).trigger("hideAllLayers");
        AJS.enableKeyboardScrolling();
        if (this.tabWatcher) {
            AJS.$(document).unbind("keydown", this.tabWatcher)
        }
    },
    addHeading: function (heading) {
        this.get$popupHeading().html(heading)
    },
    onContentReady: function (func) {
        if (AJS.$.isFunction(func)) {
            this.onContentReadyCallbacks.push(func)
        }
    }
});
AJS.popup = function (options, width, id) {
    if (typeof options !== "object") {
        options = {
            width: arguments[0],
            height: arguments[1],
            id: arguments[2]
        }
    }
    var popup = new JIRA.Dialog({
        type: "blank",
        id: options.id || id,
        width: options.width,
        cached: true
    });
    return {
        element: popup.get$popup(),
        show: function () {
            popup.show()
        },
        hide: function () {
            popup.hide()
        },
        changeSize: function () {
            popup._positionInCenter()
        },
        remove: function () {
            this.element.remove();
            this.element = null
        },
        disable: function () {},
        enable: function () {}
    }
};
JIRA.Dialog.WIDTH_PRESETS = {
    small: 360,
    medium: 540,
    large: 810
};
AJS.namespace("AJS.FlexiPopup", null, JIRA.Dialog);
JIRA.FormDialog = JIRA.Dialog.extend({
    _getDefaultOptions: function () {
        return AJS.$.extend(this._super(), {
            autoClose: false,
            targetUrl: "",
            handleRedirect: false,
            onUnSuccessfulSubmit: function () {},
            onSuccessfulSubmit: function () {},
            onDialogFinished: function () {
                if (this._hasTargetUrl()) {
                    window.location.href = this._getTargetUrlValue()
                } else {
                    AJS.reloadViaWindowLocation(window.location.href)
                }
            },
            submitAjaxOptions: {
                type: "post",
                data: {
                    inline: true,
                    decorator: "dialog"
                },
                dataType: "html"
            }
        })
    },
    _getFormDataAsObject: function () {
        var fieldValues = {};
        AJS.$(this.$form.serializeArray()).each(function () {
            var fieldVal = fieldValues[this.name];
            if (!fieldVal) {
                fieldVal = this.value
            } else {
                if (AJS.$.isArray(fieldVal)) {
                    fieldVal.push(this.value)
                } else {
                    fieldVal = [fieldVal, this.value]
                }
            }
            fieldValues[this.name] = fieldVal
        });
        return fieldValues
    },
    _getRelativePath: function () {
        return parseUri(this.options.url || this.$activeTrigger.attr("href")).directory
    },
    _getPath: function (action) {
        var relPath = this._getRelativePath();
        if (action.indexOf(relPath) == 0) {
            return action
        } else {
            return relPath + action
        }
    },
    _submitForm: function (e) {
        this.cancelled = false;
        this.xhr = null;
        this.redirected = false;
        this.serverIsDone = false;
        var instance = this,
            defaultRequestOptions = AJS.$.extend(true, {}, this.options.submitAjaxOptions),
            requestOptions = AJS.$.extend(true, defaultRequestOptions, {
                url: this._getPath(this.$form.attr("action")),
                data: this._getFormDataAsObject(),
                complete: function (xhr, textStatus, smartAjaxResult) {
                    if (!instance.cancelled) {
                        if (smartAjaxResult.successful) {
                            instance.$form.trigger("fakesubmit");
                            instance._handleServerSuccess(smartAjaxResult.data, xhr, textStatus, smartAjaxResult);
                            if (!instance.redirected) {
                                instance._handleSubmitResponse(smartAjaxResult.data, xhr, smartAjaxResult)
                            }
                        } else {
                            instance._handleServerError(xhr, textStatus, smartAjaxResult.errorThrown, smartAjaxResult)
                        }
                    }
                }
            });
        this.xhr = JIRA.SmartAjax.makeRequest(requestOptions);
        AJS.$(this.xhr).throbber({
            target: AJS.$(".throbber", this.get$popupContent())
        });
        e.preventDefault()
    },
    _handleServerError: function (xhr, textStatus, errorThrown, smartAjaxResult) {
        if (this.options.onUnSuccessfulSubmit) {
            this.options.onUnSuccessfulSubmit.call(xhr, textStatus, errorThrown, smartAjaxResult)
        }
        var errorContent = JIRA.SmartAjax.buildDialogErrorContent(smartAjaxResult, true);
        var content$ = this.get$popupContent().find(".content-body");
        if (content$.length !== 1) {
            content$ = this.get$popupContent()
        }
        var insertErrMsg = content$.length == 1 && !smartAjaxResult.hasData;
        if (insertErrMsg) {
            content$.prepend(errorContent)
        } else {
            this._setContent(errorContent)
        }
    },
    _handleServerSuccess: function (data, xhr, textStatus, smartAjaxResult) {
        var instructions = this._detectRedirectInstructions(xhr);
        this.serverIsDone = instructions.serverIsDone;
        if (instructions.redirectUrl) {
            if (this.options.onSuccessfulSubmit) {
                this.options.onSuccessfulSubmit.call(this, data, xhr, textStatus, smartAjaxResult)
            }
            this._performRedirect(instructions.redirectUrl)
        } else {
            this._setContent(data)
        }
    },
    _handleInitialDoneResponse: function (data, xhr, smartAjaxResult) {
        this._handleSubmitResponse(data, xhr, smartAjaxResult)
    },
    _handleSubmitResponse: function (data, xhr, smartAjaxResult) {
        if (this.serverIsDone) {
            if (this.options.onSuccessfulSubmit) {
                this.options.onSuccessfulSubmit.call(this, data, xhr, smartAjaxResult)
            }
            if (this.options.autoClose) {
                this.hide()
            }
            if (this.options.onDialogFinished) {
                this.options.onDialogFinished.call(this, data, xhr, smartAjaxResult)
            }
        }
    },
    _performRedirect: function (url) {
        this.hide();
        this.redirected = true;
        this._super(url)
    },
    _hasTargetUrl: function () {
        return this._getTargetUrlHolder().length > 0
    },
    _getTargetUrlHolder: function () {
        return AJS.$(this.options.targetUrl)
    },
    _getTargetUrlValue: function () {
        return this._getTargetUrlHolder().val()
    },
    decorateContent: function () {
        var instance = this,
            $formHeading, $buttons, $cancel, $buttonContainer, $closeLink;
        this.$form = AJS.$("form", this.get$popupContent());
        $formHeading = AJS.$(":header:first", this.get$popupContent());
        if ($formHeading.length > 0) {
            this.addHeading($formHeading.html());
            $formHeading.hide()
        }
        this.$form.submit(function (e) {
            if (instance.$form.trigger("before-submit", [e, instance])) {
                var submitButtons = AJS.$(":submit", instance.$form);
                submitButtons.attr("disabled", "disabled");
                instance._submitForm(e)
            }
        });
        var issueId = this.$form.find("input[name=id]").val();
        this.$form.find("input[type=file]").inlineAttach();
        $cancel = AJS.$(".cancel", this.get$popupContent());
        $cancel.click(function (e) {
            if (instance.xhr) {
                instance.xhr.abort()
            }
            instance.xhr = null;
            instance.cancelled = true;
            instance.hide();
            e.preventDefault()
        });
        if (AJS.$.browser.msie) {
            $cancel.focus(function (e) {
                if (e.altKey) {
                    $cancel.click()
                }
            })
        }
        var $popupContent = this.get$popupContent();
        $buttons = AJS.$(".button", $popupContent);
        $buttonContainer = AJS.$("div.buttons", $popupContent);
        if ($cancel.length == 0 && $buttons.length == 0) {
            if ($buttonContainer.length == 0) {
                $buttonContainer = AJS.$('<div class="buttons-container content-footer"><div class="buttons"/></div>').appendTo($popupContent)
            }
            AJS.populateParameters();
            $closeLink = AJS.$("<a href='#' class='cancel' id='aui-dialog-close'>" + AJS.params.closelink + "</a>");
            AJS.$($popupContent).find(".buttons").append($closeLink);
            $closeLink = AJS.$(".cancel", this.get$popupContent());
            $closeLink.click(function (e) {
                instance.hide();
                e.preventDefault()
            })
        }
        $buttonContainer.prepend(AJS.$("<span class='icon throbber'/>"));
        AJS.$(".shortcut-tip-trigger", $popupContent).click(function (e) {
            e.preventDefault();
            if (!$popupContent.isDirty() || confirm(AJS.params.dirtyDialogMessage)) {
                instance.hide();
                AJS.$("#keyshortscuthelp").click()
            }
        })
    },
    _setContent: function (content, decorate) {
        this._super(content, decorate);
        if (content && JIRA.Dialog.current === this) {
            this._focusFirstField()
        }
    },
    _focusFirstField: function () {
        var triggerConfig = new JIRA.setFocus.FocusConfiguration();
        triggerConfig.context = this.get$popup()[0];
        if (AJS.$.browser.msie) {
            var $focusHack = AJS.$(".trigger-hack", triggerConfig.context);
            if ($focusHack.length === 0) {
                $focusHack = AJS.$("<input Class='trigger-hack' type='text' value=''/>").css({
                    position: "absolute",
                    left: -9000
                }).appendTo(triggerConfig.context)
            }
            $focusHack.focus()
        }
        JIRA.setFocus.pushConfiguration(triggerConfig);
        JIRA.setFocus.triggerFocus();
        JIRA.setFocus.triggerFocus()
    },
    hide: function (undim) {
        if (this._super(undim) === false) {
            return false
        }
        JIRA.setFocus.popConfiguration()
    }
});
AJS.namespace("AJS.FormPopup", null, JIRA.FormDialog);
JIRA.IssueActionsDialog = JIRA.Dialog.extend({
    _getDefaultOptions: function () {
        return AJS.$.extend(this._super(), {
            cached: false,
            id: "issue-actions-dialog",
            widthClass: "small"
        })
    },
    _setContent: function (content, decorate) {
        if (content) {
            this._super(content, decorate)
        } else {
            this._super(AJS.$(["<form id='issue-actions-dialog-form' class='aui'>", "<div class='content-body'>", "<div id='issueactions-suggestions' class='aui-list' />", "<div class='description'>", AJS.params.issueActionsHint, "</div>", "</div>", "</form>"].join("")), true)
        }
        if (JIRA.Dialog.current === this) {
            var triggerConfig = new JIRA.setFocus.FocusConfiguration();
            triggerConfig.context = this.get$popup()[0];
            triggerConfig.parentElementSelectors = [".content-body"];
            JIRA.setFocus.pushConfiguration(triggerConfig);
            JIRA.setFocus.triggerFocus()
        }
    },
    _formatActionsResponse: function (response) {
        function addSelected(issueId) {
            var url = window.location.href,
                newUrl = url;
            if (/selectedIssueId=[0-9]*/.test(url)) {
                newUrl = newUrl.replace(/selectedIssueId=[0-9]*/g, "selectedIssueId=" + issueId)
            } else {
                if (JIRA.IssueNavigator.isNavigator()) {
                    if (/\?/.test(url)) {
                        newUrl = newUrl + "&"
                    } else {
                        newUrl = newUrl + "?"
                    }
                    newUrl = newUrl + "selectedIssueId=" + issueId
                }
            }
            return encodeURIComponent(newUrl)
        }
        function formatWorkflowResponse(workflowResponse) {
            var workflows = new AJS.GroupDescriptor({
                label: AJS.params.workflow
            });
            AJS.$(workflowResponse).each(function () {
                workflows.addItem(new AJS.ItemDescriptor({
                    href: contextPath + "/secure/WorkflowUIDispatcher.jspa?id=" + response.id + "&action=" + this.action + "&atl_token=" + response.atlToken + "&returnUrl=" + addSelected(response.id),
                    label: this.name,
                    styleClass: "issueaction-workflow-transition"
                }))
            });
            return workflows
        }
        function formatOperationResonse(operationsResponse) {
            var operations = new AJS.GroupDescriptor({
                label: AJS.params.actions
            });
            AJS.$(operationsResponse).each(function () {
                var _returnUrl;
                if (this.name === "Clone") {
                    if (JIRA.IssueNavigator.isNavigator()) {
                        _returnUrl = addSelected(response.id)
                    } else {
                        _returnUrl = ""
                    }
                } else {
                    _returnUrl = addSelected(response.id)
                }
                operations.addItem(new AJS.ItemDescriptor({
                    href: this.url + "&returnUrl=" + _returnUrl,
                    label: this.name,
                    styleClass: this.styleClass
                }))
            });
            return operations
        }
        var res = [];
        if (response) {
            if (response.actions && response.actions.length != 0) {
                res.push(formatWorkflowResponse(response.actions))
            }
            if (response.operations && response.operations.length != 0) {
                res.push(formatOperationResonse(response.operations))
            }
        }
        return res
    },
    decorateContent: function () {
        var instance = this,
            issueKey = JIRA.IssueNavigator.getSelectedIssueKey(),
            issueId = JIRA.Issue.getIssueId() || JIRA.IssueNavigator.getSelectedIssueId();
        if (issueKey) {
            this.addHeading(AJS.params.dotOperations + ": <span>" + issueKey + "</span>")
        } else {
            this.addHeading(AJS.params.dotOperations)
        }
        this.queryControl = new AJS.QueryableDropdownSelect({
            id: "issueactions",
            element: this.$content.find("#issueactions-suggestions"),
            ajaxOptions: {
                minQueryLength: 1,
                dataType: "json",
                url: AJS.format(contextPath + "/rest/api/1.0/issues/{0}/ActionsAndOperations?atl_token={1}", issueId, atl_token()),
                formatResponse: this._formatActionsResponse
            },
            showDropdownButton: true,
            loadOnInit: true
        });
        this.queryControl._handleServerError = function (smartAjaxResult) {
            var errMsg = JIRA.SmartAjax.buildSimpleErrorContent(smartAjaxResult);
            var errorClass = smartAjaxResult.status === 401 ? "warn" : "error";
            instance._setContent(AJS.$('<div class="ajaxerror"><div class="notify ' + errorClass + '"><p>' + errMsg + "</p></div></div>"), false);
            instance._addCloseLink()
        };
        this.timeoutId = undefined;
        this._addCloseLink()
    },
    _addCloseLink: function () {
        var instance = this,
            $closeLink, $buttonContainer, $buttons;
        $buttonContainer = AJS.$('<div class="buttons-container content-footer"></div>').appendTo(this.get$popupContent());
        $buttons = AJS.$('<div class="buttons"/>').appendTo($buttonContainer);
        $closeLink = AJS.$("<a href='#' class='cancel' id='aui-dialog-close'>" + AJS.params.closelink + "</a>");
        $closeLink.appendTo($buttons, this.get$popupContent()).click(function (e) {
            instance.hide();
            e.preventDefault()
        });
        this.get$popupContent().append($buttonContainer)
    },
    hide: function (undim) {
        if (this._super(undim) === false) {
            return false
        }
        JIRA.setFocus.popConfiguration()
    }
});
AJS.namespace("AJS.IssueActionsPopup", null, JIRA.IssueActionsDialog);
JIRA.LabelsDialog = JIRA.FormDialog.extend((function () {
    var impl = {};
    impl.init = function (options) {
        this._super(options);
        this.issueId = null;
        this.customFieldId = null;
        this.labelsProvider = this.initLabelsProvider();
        this.labelPicker = null
    }, impl.initLabelsProvider = function () {
        if (this.options.labelsProvider && AJS.$.isFunction(this.options.labelsProvider)) {
            return this.options.labelsProvider
        } else {
            if (this.options.labels) {
                return this._getLabelsFromOptions
            } else {
                return this._getLabelsFromTrigger
            }
        }
    }, impl._getLabelsFromOptions = function () {
        return AJS.$(this.options.labels)
    }, impl._getLabelsFromTrigger = function () {
        return this.$activeTrigger.closest(".labels-wrap")
    }, impl.decorateContent = function () {
        this._super();
        var $content = this.get$popupContent();
        this.issueId = $content.find("input[name=id]").val();
        var $customFieldId = $content.find("input[name=customFieldId]");
        if ($customFieldId.length === 1) {
            this.customFieldId = $customFieldId.val()
        } else {
            this.customFieldId = null
        }
    };
    impl.focusLabelPicker = function () {
        this.get$popupContent().find("textarea").focus()
    };
    impl.show = function () {
        if (this._super()) {
            this.focusLabelPicker()
        }
    };
    impl._handleSubmitResponse = function (data, xhr, smartAjaxResult) {
        if (this.serverIsDone) {
            if (this.options.onSuccessfulSubmit) {
                this.options.onSuccessfulSubmit.call(this, data, xhr, smartAjaxResult)
            }
            var issueIdVal = this.get$popupContent().find("input[name=id]").val(),
                noLinkVal = this.get$popupContent().find("input[name=noLink]").val();
            if (this.options.autoClose) {
                this.hide()
            }
            JIRA.IssueNavigator.Shortcuts.flashIssueRow(this.issueId);
            var postData = {
                id: issueIdVal,
                decorator: "none",
                noLink: noLinkVal
            };
            if (this.customFieldId) {
                postData.customFieldId = this.customFieldId
            }
            var instance = this;
            var $labelsWrap = instance.labelsProvider(this);
            if ($labelsWrap) {
                jQuery(jQuery.ajax({
                    url: contextPath + "/secure/EditLabels!viewLinks.jspa",
                    data: postData,
                    success: function (html) {
                        var $newLabelsWrap = jQuery("<div>").html(html).find(".labels-wrap");
                        if (JIRA.IssueNavigator.isNavigator()) {
                            $newLabelsWrap.find("a.edit-labels").remove()
                        }
                        $labelsWrap.html($newLabelsWrap.html())
                    }
                })).throbber({
                    target: $labelsWrap
                })
            }
        }
    };
    impl.handleCancel = function () {
        this._super();
        this.$content = null
    };
    return impl
})());
AJS.namespace("AJS.LabelsPopup", null, JIRA.LabelsDialog);
JIRA.ScreenshotDialog = function (options) {
    var self = this;
    this.$trigger = jQuery(options.trigger);
    this.$trigger.click(function (e) {
        e.preventDefault();
        self.openWindow()
    })
};
JIRA.ScreenshotDialog.prototype.openWindow = function () {
    if (JIRA.Dialog.current) {
        JIRA.Dialog.current.hide()
    }
    if (AJS.InlineLayer.current) {
        AJS.InlineLayer.current.hide()
    }
    window.open(this.$trigger.attr("href"), "screenshot", "width=800,height=700,scrollbars=yes,status=yes")
};
AJS.namespace("jira.app.attachments.screenshot.ScreenshotWindow", null, JIRA.ScreenshotDialog);
JIRA.userhover = function (context) {
    AJS.$("a.user-hover", context).bind({
        "mouseenter": function () {
            var trigger = this,
                options = JIRA.userhover.INLINE_DIALOG_OPTIONS,
                control = AJS.$.data(trigger, "AJS.InlineDialog") || AJS.$.data(trigger, "AJS.InlineDialog", {});
            control.hasUserAttention = true;
            clearTimeout(control.timerId);
            control.timerId = setTimeout(function () {
                if (control.show) {
                    control.show()
                } else {
                    var $trigger = AJS.$(trigger),
                        $popup = AJS.InlineDialog($trigger, "user-hover-dialog-" + new Date().getTime(), function ($contents, control, show) {
                            JIRA.userhover.fetchDialogContents($contents, $trigger, show)
                        }, options);
                    $popup.show();
                    $popup.find(".contents").bind({
                        "mouseenter": function () {
                            control.hasUserAttention = true
                        },
                        "mouseleave": function (e) {
                            if (!AJS.InlineLayer.current) {
                                $trigger.mouseleave()
                            }
                        }
                    });
                    control.show = $popup.show;
                    control.hide = $popup.hide
                }
            }, options.showDelay)
        },
        "mouseleave": function () {
            var control = AJS.$.data(this, "AJS.InlineDialog");
            control.hasUserAttention = false;
            clearTimeout(control.timerId);
            if (control.hide) {
                control.timerId = setTimeout(function () {
                    if (!control.hasUserAttention) {
                        if (AJS.dropDown.current) {
                            AJS.dropDown.current.hide()
                        }
                        control.hide()
                    }
                }, JIRA.userhover.INLINE_DIALOG_OPTIONS.showDelay)
            }
        }
    })
};
JIRA.userhover.INLINE_DIALOG_OPTIONS = {
    urlPrefix: contextPath + "/secure/ViewUserHover!default.jspa?decorator=none&username=",
    showDelay: 400,
    closeOthers: false,
    noBind: true,
    hideCallback: function () {
        if (AJS.dropDown.current && AJS.$(AJS.dropDown.current.trigger).parents(".aui-inline-dialog").length > 0) {
            AJS.dropDown.current.hide()
        }
    }
};
JIRA.userhover.fetchDialogContents = function ($contents, $trigger, firstShow) {
    var trigger = $trigger[0],
        options = JIRA.userhover.INLINE_DIALOG_OPTIONS,
        control = AJS.$.data(trigger, "AJS.InlineDialog");
    if (!control.hasContent) {
        control.hasContent = true;
        AJS.$.get(options.urlPrefix + $trigger.attr("rel"), function (html) {
            if (control.hasUserAttention) {
                firstShow()
            } else {
                var show = control.show;
                control.show = function () {
                    firstShow();
                    control.show = show
                }
            }
            $contents.html(html);
            $contents.css("overflow", "visible");
            AJS.Dropdown.create({
                trigger: $contents.find(".aui-dd-link"),
                content: $contents.find(".aui-list")
            });
            $contents.bind("click", options.hideCallback)
        })
    }
};
AJS.$(function () {
    JIRA.userhover(document.body)
});
AJS.namespace("jira.app.userhover", null, JIRA.userhover);
AJS.InlineAttach = Class.extend({
    _getDefaultOptions: function () {
        return {
            postUrl: contextPath + "/secure/AttachTemporaryFile.jspa"
        }
    },
    init: function (options) {
        var that = this;
        if (typeof options === "string") {
            options = {
                element: options
            }
        }
        options = options || {};
        this.options = AJS.$.extend(this._getDefaultOptions(), options);
        this.$container = AJS.$(this.options.element);
        this.$form = this.$container.closest("form");
        this.$container.change(function () {
            that.attachFile(AJS.$(this))
        })
    },
    _getFilename: function (fileName) {
        if (AJS.$.browser.msie && fileName.indexOf("\\") >= 0) {
            fileName = fileName.substring(fileName.lastIndexOf("\\") + 1)
        }
        return fileName
    },
    _getAtlToken: function ($element) {
        if ($element) {
            var $form = $element.closest("form");
            var $atlToken = AJS.$("input[name='atl_token']", $form);
            if ($atlToken.length > 0) {
                return $atlToken.val()
            }
        }
        return atl_token()
    },
    attachFile: function ($input) {
        var that = this,
            $fileInputContainer = $input.parent(),
            $originalFileInput = this._renders.fileInput($input),
            $attachForm = this._renders.attachForm(this.options.postUrl).append($input),
            $loadingSpan = this._renders.loadingSpan(this._getFilename($input.val())),
            postData = {
                atl_token: this._getAtlToken($fileInputContainer)
            };
        $originalFileInput.change(function () {
            that.attachFile(AJS.$(this))
        });
        $fileInputContainer.parent().find("div.error").remove();
        AJS.$("body").append($attachForm);
        if (this.options.issueId) {
            postData.id = this.options.issueId
        } else {
            postData.create = true;
            postData.projectId = this.options.projectId
        }
        $attachForm.ajaxForm({
            dataType: "json",
            data: postData,
            timeout: 0,
            cleanupAfterSubmit: function () {
                $loadingSpan.remove();
                if (that.$form.find(".loading.file").length === 0) {
                    that.$form.find("input[type=submit]").removeAttr("disabled")
                }
                $attachForm.remove()
            },
            beforeSubmit: function () {
                $fileInputContainer.append($loadingSpan);
                if ($fileInputContainer.is("td")) {
                    $fileInputContainer.append(AJS.$("<div class='field-group'/>").append($originalFileInput))
                } else {
                    $fileInputContainer.after(AJS.$("<div class='field-group'/>").append($originalFileInput))
                }
                if (AJS.$.browser.msie) {
                    setTimeout(function () {
                        $originalFileInput.focus()
                    }, 0)
                } else {
                    $originalFileInput.focus()
                }
                that.$form.find("input[type=submit]").attr("disabled", "true")
            },
            error: function (xhr) {
                if (xhr && xhr.responseText && xhr.responseText.indexOf("SecurityTokenMissing") >= 0) {
                    $loadingSpan.after(that._renders.errors(AJS.params.attachmentsXsrfTimeout))
                } else {
                    $loadingSpan.after(that._renders.errors(AJS.params.attachmentsUnknownError))
                }
                $fileInputContainer.addClass("error");
                this.cleanupAfterSubmit()
            },
            success: function (response) {
                if (response.id) {
                    $loadingSpan.after(that._renders.temporaryFileCheckbox(response))
                } else {
                    if (response.errorMsg) {
                        $loadingSpan.after(that._renders.errors(response.errorMsg));
                        $fileInputContainer.addClass("error")
                    }
                }
                this.cleanupAfterSubmit()
            }
        });
        $attachForm.submit()
    },
    _renders: {
        attachForm: function (postUrl) {
            return AJS.$("<form method='post' enctype='multipart/form-data' action='" + postUrl + "'/>").hide()
        },
        fileInput: function ($originalInput) {
            return $originalInput.clone().val("")
        },
        loadingSpan: function (fileName) {
            return AJS.$("<div class='loading file'>" + fileName + "</div>")
        },
        temporaryFileCheckbox: function (response) {
            return AJS.$("<input type='checkbox' class='checkbox' name='filetoconvert' value='" + response.id + "' title='" + response.title + "' checked><label>" + response.name + "</label>")
        },
        errors: function (errorMsg) {
            return AJS.$("<div>" + errorMsg + "</div>")
        }
    }
});
jQuery.fn.inlineAttach = function (options) {
    var res = [];
    this.each(function () {
        options = options || {};
        options.element = this;
        var context = AJS.$(this).closest("form");
        options.issueId = AJS.$("input[name=id]", context).val();
        options.projectId = AJS.$("input[name=pid]", context).val();
        res.push(new AJS.InlineAttach(options))
    });
    return res
};
JIRA.setFocus = (function () {
    var _defaultExcludeParentSelector = "form.dont-default-focus",
        _defaultFocusElementSelector = "input:not(#issue-filter-submit), select, textarea, button, a.cancel",
        _defaultParentElementSelectors = ["div.aui-popup-content:visible", "form.aui:visible", "form:visible"],
        _configurationStack = [];
    var _focusIn = function (context, parentSelector, excludeParentSelector, elementSelector) {
            var found = false;
            AJS.$(parentSelector, context).not(excludeParentSelector).find(elementSelector).each(function () {
                var elem = AJS.$(this);
                if (elem.is(":visible:enabled, a:visible")) {
                    elem.focus();
                    if (elem.is(":text, :password, textarea")) {
                        if (elem.is(".focus-select-end")) {
                            elem.setCaretToPosition(elem[0].value.length)
                        } else {
                            elem.setSelectionRange(0, elem[0].value.length)
                        }
                    }
                    found = true;
                    return false
                }
            });
            return found
        },
        _defaultFocusNow = function () {
            var i = 0,
                currentConfig = _configurationStack[_configurationStack.length - 1];
            while (!_focusIn(currentConfig.context, currentConfig.parentElementSelectors[i], currentConfig.excludeParentSelector, currentConfig.focusElementSelector) && i < currentConfig.parentElementSelectors.length) {
                i++
            }
        };
    return {
        FocusConfiguration: function () {
            this.context = document;
            this.excludeParentSelector = _defaultExcludeParentSelector;
            this.focusElementSelector = _defaultFocusElementSelector;
            this.parentElementSelectors = _defaultParentElementSelectors;
            this.focusNow = _defaultFocusNow
        },
        triggerFocus: function () {
            if (_configurationStack.length == 0) {
                _configurationStack.push(new this.FocusConfiguration())
            }
            _configurationStack[_configurationStack.length - 1].focusNow()
        },
        pushConfiguration: function (configuration) {
            _configurationStack.push(configuration)
        },
        popConfiguration: function () {
            _configurationStack.pop()
        }
    }
})();
jQuery(function () {
    JIRA.setFocus.triggerFocus()
});
(function () {
    var eventsToListenTo = "input keyup propertychange";
    jQuery.fn.expandOnInput = function (maxHeight) {
        var $textareas = this.filter("textarea");
        $textareas.unbind(eventsToListenTo, setHeight).bind(eventsToListenTo, setHeight);
        if (AJS.$.browser.mozilla || AJS.$.browser.msie) {
            $textareas.unbind("paste", triggerKeyup).bind("paste", triggerKeyup)
        }
        $textareas.unbind("refreshInputHeight").bind("refreshInputHeight", function () {
            setHeight.call(AJS.$(this).css("height", ""))
        });
        $textareas.data("expandOnInput_maxHeight", maxHeight);
        $textareas.each(function () {
            var $this = AJS.$(this);
            $this.each(function () {
                var $this = AJS.$(this);
                $this.data("hasFixedParent", $this.hasFixedParent())
            });
            if (AJS.$(this).val() !== "") {
                setHeight.call(this)
            }
        });
        return this
    };

    function triggerKeyup() {
        var $textarea = AJS.$(this),
            textarea = this;
        setTimeout(function () {
            $textarea.keyup();
            textarea.scrollTop = textarea.scrollHeight
        }, 0)
    }
    function setHeight() {
        var $textarea = AJS.$(this),
            height = parseInt($textarea.css("height"), 10) || $textarea.height(),
            padding = $textarea.attr("clientHeight") - height;
        this.scrollHeight;
        var maxHeight = parseInt($textarea.css("maxHeight"), 10) || $textarea.data("expandOnInput_maxHeight") || AJS.$(window).height() - 160,
            newHeight = Math.max(height, this.scrollHeight - padding);
        if (newHeight < maxHeight) {
            $textarea.css({
                "overflow": "hidden",
                "height": newHeight + "px"
            })
        } else {
            var cursorPosition = this.selectionStart;
            $textarea.css({
                "overflow-y": "auto",
                "height": maxHeight + "px"
            });
            if (AJS.$.browser.msie && AJS.$.browser.version <= 7) {
                setTimeout(function () {
                    $textarea.css({
                        "zoom": "1"
                    })
                }, 0)
            }
            $textarea.unbind(eventsToListenTo, setHeight);
            $textarea.unbind("paste", triggerKeyup);
            if (this.selectionStart !== cursorPosition) {
                this.selectionStart = cursorPosition;
                this.selectionEnd = cursorPosition
            }
            newHeight = maxHeight
        }
        if (!$textarea.data("hasFixedParent")) {
            var $window = AJS.$(window),
                scrollTop = $window.scrollTop(),
                minScrollTop = $textarea.offset().top + newHeight - $window.height() + 29;
            if (scrollTop < minScrollTop) {
                $window.scrollTop(minScrollTop)
            }
        }
        $textarea.trigger("stalkerHeightUpdated")
    }
})();
AJS.$.fn.isDirty = function () {
    var isClean = true;
    this.find("form").add(this.filter("form")).each(function () {
        var initValue = AJS.$.data(this, AJS.DIRTY_FORM_VALUE);
        return isClean = initValue == null || initValue == AJS.$(this).find(":input[name!=atl_token]").serialize()
    });
    return !isClean
};
(function () {
    var $doc = AJS.$(document);
    var excluded = $doc;
    var oldOnbeforeunload = window.onbeforeunload;
    window.onbeforeunload = function () {
        if (oldOnbeforeunload) {
            oldOnbeforeunload()
        }
        if (AJS.$("form").not(excluded).isDirty() && !AJS.isSelenium()) {
            return AJS.params.dirtyMessage
        }
    };
    $doc.delegate(":input", "focus", function () {
        var theForm = this.form;
        setTimeout(function () {
            initForm.call(theForm)
        }, 50)
    });
    AJS.$(function () {
        setTimeout(function () {
            AJS.$("form").each(initForm)
        }, 50);
        $doc.delegate("*", "submit fakesubmit", muteWarnings);
        AJS.$("form").bind("submit", muteWarnings)
    });

    function initForm() {
        if (AJS.$.data(this, AJS.DIRTY_FORM_VALUE)) {
            return
        }
        if (AJS.$(this).is("#jqlform, #quicksearch, #issue-create-quick, #issue-actions-dialog-form, .userpref-form")) {
            return
        }
        AJS.$.data(this, AJS.DIRTY_FORM_VALUE, AJS.$(this).find(":input[name!=atl_token]").serialize())
    }
    $doc.delegate("a.cancel,#cancelButton,#refresh-dependant-fields,form#tabForm table#field_table a", "mousedown keydown click", muteWarnings);

    function muteWarnings() {
        excluded = this.form || AJS.$(this).closest("form");
        if (this.id === "cancelButton") {
            AJS.$(this).one("click", restoreWarnings)
        } else {
            restoreWarnings()
        }
    }
    function restoreWarnings() {
        setTimeout(function () {
            excluded = $doc
        }, 500)
    }
})();
jQuery.fn.overlabel = function () {
    this.each(function () {
        var label = AJS.$(this).removeClass("overlabel").addClass("overlabel-apply show").click(function () {
            AJS.$("#" + AJS.$(this).attr("for")).focus()
        });
        var field = AJS.$("#" + label.attr("for")).focus(function () {
            label.removeClass("show").hide()
        }).blur(function () {
            if (AJS.$(this).val() === "") {
                label.addClass("show").show()
            }
        });
        if (field.val() !== "") {
            label.removeClass("show").hide()
        }
    });
    return this
};
