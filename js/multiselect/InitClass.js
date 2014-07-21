if (typeof jQuery != "undefined") {
    var NJS = (function () {
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
                if (!(E = $(E))) {
                    return
                }
                E.toggleClass(F)
            },
            setVisible: function (F, E) {
                if (!(F = $(F))) {
                    return
                }
                var G = $;
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
                if (!(E = $(E))) {
                    return
                }
                if (F) {
                    E.addClass("current")
                } else {
                    E.removeClass("current")
                }
            },
            isVisible: function (E) {
                return !$(E).hasClass("hidden")
            },
            populateParameters: function () {
                var E = this;
                $(".parameters input").each(function () {
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
                $(function () {
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
                NJS.format = function (K) {
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
                return NJS.format.apply(NJS, arguments)
            },
            firebug: function () {
                var E = $(document.createElement("script"));
                E.attr("src", "http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js");
                $("head").append(E);
                (function () {
                    if (window.firebug) {
                        firebug.init()
                    } else {
                        setTimeout(arguments.callee, 0)
                    }
                })()
            },
            clone: function (E) {
                return $(E).clone().removeAttr("id")
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
                if (NJS.dim.dim) {
                    NJS.dim.dim.remove();
                    NJS.dim.dim = null
                } else {
                    NJS.dim.dim = NJS("div").css({
                        width: "100%",
                        height: $(document).height(),
                        background: "#000",
                        opacity: 0.5,
                        position: "absolute",
                        top: 0,
                        left: 0
                    });
                    $("body").append(NJS.dim.dim)
                }
            },
            onTextResize: function (F) {
                if (typeof F == "function") {
                    if (NJS.onTextResize["on-text-resize"]) {
                        NJS.onTextResize["on-text-resize"].push(function (G) {
                            F(G)
                        })
                    } else {
                        var E = NJS("div");
                        E.css({
                            width: "1em",
                            height: "1em",
                            position: "absolute",
                            top: "-9999em",
                            left: "-9999em"
                        });
                        $("body").append(E);
                        E.size = E.width();
                        setInterval(function () {
                            if (E.size != E.width()) {
                                E.size = E.width();
                                for (var G = 0, H = NJS.onTextResize["on-text-resize"].length; G < H; G++) {
                                    NJS.onTextResize["on-text-resize"][G](E.size)
                                }
                            }
                        }, 0);
                        NJS.onTextResize.em = E;
                        NJS.onTextResize["on-text-resize"] = [function (G) {
                            F(G)
                        }]
                    }
                }
            },
            unbindTextResize: function (G) {
                for (var E = 0, F = NJS.onTextResize["on-text-resize"].length; E < F; E++) {
                    if (NJS.onTextResize["on-text-resize"][E] == G) {
                        return NJS.onTextResize["on-text-resize"].splice(E, 1)
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
                var G = $;
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
                options = $.extend(H, options);
                var L = options.scaleFactor || 1,
                    K = options.fill || "#fff",
                    J = options.stroke || "#000",
                    G = 400 * L,
                    E = 40 * L;
                strokeWidth = options.strokeWidth || 1;
                if ($(".aui-logo").size() == 0) {
                    $("body").append("<div id='aui-logo' class='aui-logo'><div>")
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
        if (typeof NJS != "undefined") {
            for (var B in NJS) {
				debugger;
                C[B] = NJS[B]
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
    $(function () {
        NJS.populateParameters()
    })
}

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
            var interfaces = $.makeArray(arguments);
            prop = interfaces.pop();
            var completeInterface;
            $.each(interfaces, function (i, inter) {
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
		// Set initializing to true
		// Avoid running constructor function more than one time
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
                    $.each(_super[name], function (name, obj) {
                        if (!newObj[name]) {
                            newObj[name] = obj
                        } else {
                            if (typeof newObj[name] === "object") {
                                var newSubObj = begetObject(newObj[name]);
                                $.each(obj, function (subName, subObj) {
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


NJS.copyObject = function (object, deep) {
    var copiedObject = {};
    $.each(object, function (name, property) {
        if (typeof property !== "object" || property === null || property instanceof $) {
            copiedObject[name] = property
        } else {
            if (deep !== false) {
                copiedObject[name] = NJS.copyObject(property, deep)
            }
        }
    });
    return copiedObject
};