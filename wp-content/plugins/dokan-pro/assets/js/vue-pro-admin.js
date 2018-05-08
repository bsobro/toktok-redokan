/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 17);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file.
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = injectStyles
  }

  if (hook) {
    var functional = options.functional
    var existing = functional
      ? options.render
      : options.beforeCreate

    if (!functional) {
      // inject component registration as beforeCreate hook
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    } else {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return existing(h, context)
      }
    }
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

var ListTable = dokan_get_lib('ListTable');
var Switches = dokan_get_lib('Switches');

/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'Vendors',

    components: {
        ListTable: ListTable,
        Switches: Switches
    },

    data: function data() {
        return {
            showCb: true,

            counts: {
                pending: 0,
                approved: 0,
                all: 0
            },

            totalItems: 0,
            perPage: 20,
            totalPages: 1,
            loading: false,

            columns: {
                'store_name': {
                    label: 'Store',
                    sortable: true
                },
                'email': {
                    label: 'E-mail'
                },
                'phone': {
                    label: 'Phone'
                },
                'registered': {
                    label: 'Registered',
                    sortable: true
                },
                'enabled': {
                    label: 'Status'
                }
            },
            actionColumn: 'title',
            actions: [{
                key: 'edit',
                label: 'Edit'
            }, {
                key: 'products',
                label: 'Products'
            }, {
                key: 'orders',
                label: 'Orders'
            }],
            bulkActions: [{
                key: 'approved',
                label: 'Approve Vendors'
            }, {
                key: 'pending',
                label: 'Disable Selling'
            }],
            vendors: []
        };
    },


    watch: {
        '$route.query.status': function $routeQueryStatus() {
            this.fetchVendors();
        },
        '$route.query.page': function $routeQueryPage() {
            this.fetchVendors();
        },
        '$route.query.orderby': function $routeQueryOrderby() {
            this.fetchVendors();
        },
        '$route.query.order': function $routeQueryOrder() {
            this.fetchVendors();
        }
    },

    computed: {
        currentStatus: function currentStatus() {
            return this.$route.query.status || 'all';
        },
        currentPage: function currentPage() {
            var page = this.$route.query.page || 1;

            return parseInt(page);
        },
        sortBy: function sortBy() {
            return this.$route.query.orderby || 'registered';
        },
        sortOrder: function sortOrder() {
            return this.$route.query.order || 'desc';
        }
    },

    created: function created() {

        this.fetchVendors();
    },


    methods: {
        updatedCounts: function updatedCounts(xhr) {
            this.counts.pending = parseInt(xhr.getResponseHeader('X-Status-Pending'));
            this.counts.approved = parseInt(xhr.getResponseHeader('X-Status-Approved'));
            this.counts.all = parseInt(xhr.getResponseHeader('X-Status-All'));
        },
        updatePagination: function updatePagination(xhr) {
            this.totalPages = parseInt(xhr.getResponseHeader('X-WP-TotalPages'));
            this.totalItems = parseInt(xhr.getResponseHeader('X-WP-Total'));
        },
        fetchVendors: function fetchVendors() {
            var _this = this;

            var self = this;

            self.loading = true;

            // dokan.api.get('/stores?per_page=' + this.perPage + '&page=' + this.currentPage + '&status=' + this.currentStatus)
            dokan.api.get('/stores', {
                per_page: this.perPage,
                page: this.currentPage,
                status: this.currentStatus,
                orderby: this.sortBy,
                order: this.sortOrder
            }).done(function (response, status, xhr) {
                // console.log(response, status, xhr);
                self.vendors = response;
                self.loading = false;

                _this.updatedCounts(xhr);
                _this.updatePagination(xhr);
            });
        },
        onActionClick: function onActionClick(action, row) {
            if ('trash' === action) {
                if (confirm('Are you sure to delete?')) {
                    alert('deleted: ' + row.title);
                }
            }
        },
        onSwitch: function onSwitch(status, vendor_id) {
            var _this2 = this;

            var message = status === false ? 'The vendor has been disabled.' : 'Selling has been enabled';

            dokan.api.put('/stores/' + vendor_id + '/status', {
                status: status === false ? 'inactive' : 'active'
            }).done(function (response) {
                _this2.$notify({
                    title: 'Success!',
                    type: 'success',
                    text: message
                });

                if (_this2.currentStatus !== 'all') {
                    _this2.fetchVendors();
                }
            });
        },
        moment: function (_moment) {
            function moment(_x) {
                return _moment.apply(this, arguments);
            }

            moment.toString = function () {
                return _moment.toString();
            };

            return moment;
        }(function (date) {
            return moment(date);
        }),
        goToPage: function goToPage(page) {
            this.$router.push({
                name: 'Vendors',
                query: {
                    status: this.currentStatus,
                    page: page
                }
            });
        },
        onBulkAction: function onBulkAction(action, items) {
            var _this3 = this;

            var jsonData = {};
            jsonData[action] = items;

            this.loading = true;

            dokan.api.put('/stores/batch', jsonData).done(function (response) {
                _this3.loading = false;
                _this3.fetchVendors();
            });
        },
        sortCallback: function sortCallback(column, order) {
            this.$router.push({
                name: 'Vendors',
                query: {
                    status: this.currentStatus,
                    page: 1,
                    orderby: column,
                    order: order
                }
            });
        },
        productUrl: function productUrl(id) {
            return dokan.urls.adminRoot + 'edit.php?post_type=product&author=' + id;
        },
        ordersUrl: function ordersUrl(id) {
            return dokan.urls.adminRoot + 'edit.php?post_type=shop_order&author=' + id;
        },
        editUrl: function editUrl(id) {
            return dokan.urls.adminRoot + 'user-edit.php?user_id=' + id;
        }
    }
});

/***/ }),
/* 5 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

var ContentLoading = dokan_get_lib('ContentLoading');
var Modal = dokan_get_lib('Modal');

var VclFacebook = ContentLoading.VclFacebook;
var VclTwitch = ContentLoading.VclTwitch;

/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'VendorSingle',

    components: {
        VclFacebook: VclFacebook,
        VclTwitch: VclTwitch,
        Modal: Modal
    },

    data: function data() {
        return {
            showDialog: false,
            store: {},
            stats: null,
            mail: {
                subject: '',
                body: ''
            }
        };
    },


    computed: {
        id: function id() {
            return this.$route.params.id;
        },
        mailTo: function mailTo() {
            return this.store.store_name + ' <' + this.store.email + '>';
        },
        hasBank: function hasBank() {
            if (this.store.payment.hasOwnProperty('bank') && !_.isEmpty(this.store.payment.bank)) {
                return true;
            }

            return false;
        }
    },

    watch: {
        '$route.params.id': function $routeParamsId() {
            this.fetch();
            this.fetchStats();
        }
    },

    created: function created() {
        this.fetch();
        this.fetchStats();
    },


    methods: {
        fetch: function fetch() {
            var _this = this;

            dokan.api.get('/stores/' + this.id).done(function (response) {
                return _this.store = response;
            });
        },
        fetchStats: function fetchStats() {
            var _this2 = this;

            dokan.api.get('/stores/' + this.id + '/stats').done(function (response) {
                return _this2.stats = response;
            });
        },
        isSocialActive: function isSocialActive(profile) {
            if (this.store.social.hasOwnProperty(profile) && this.store.social[profile] !== false) {
                return true;
            }

            return false;
        },
        hasPaymentEmail: function hasPaymentEmail(method) {
            if (this.store.payment.hasOwnProperty(method) && this.store.payment[method].email !== false) {
                return true;
            }

            return false;
        },
        messageDialog: function messageDialog() {
            this.showDialog = true;
        },
        sendEmail: function sendEmail() {
            var _this3 = this;

            this.showDialog = false;

            dokan.api.post('/stores/' + this.id + '/email', {
                subject: this.mail.subject,
                body: this.mail.body
            }).done(function (response) {
                _this3.$notify({
                    title: 'Success!',
                    type: 'success',
                    text: 'Email has been sent successfully.'
                });
            });

            this.mail = {
                subject: '',
                body: ''
            };
        },
        moment: function (_moment) {
            function moment(_x) {
                return _moment.apply(this, arguments);
            }

            moment.toString = function () {
                return _moment.toString();
            };

            return moment;
        }(function (date) {
            return moment(date);
        }),
        productUrl: function productUrl() {
            return dokan.urls.adminRoot + 'edit.php?post_type=product&author=' + this.store.id;
        },
        ordersUrl: function ordersUrl() {
            return dokan.urls.adminRoot + 'edit.php?post_type=shop_order&author=' + this.store.id;
        },
        editUrl: function editUrl() {
            return dokan.urls.adminRoot + 'user-edit.php?user_id=' + this.store.id;
        }
    }
});

/***/ }),
/* 6 */,
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */,
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _Vendors = __webpack_require__(18);

var _Vendors2 = _interopRequireDefault(_Vendors);

var _VendorSingle = __webpack_require__(21);

var _VendorSingle2 = _interopRequireDefault(_VendorSingle);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

dokan_add_route(_Vendors2.default);
dokan_add_route(_VendorSingle2.default);

/***/ }),
/* 18 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Vendors_vue__ = __webpack_require__(4);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7a477aab_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Vendors_vue__ = __webpack_require__(20);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(19)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Vendors_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7a477aab_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Vendors_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/Vendors.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7a477aab", Component.options)
  } else {
    hotAPI.reload("data-v-7a477aab", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 19 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 20 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "vendor-list" },
    [
      _c("h1", { staticClass: "wp-heading-inline" }, [_vm._v("Vendors")]),
      _vm._v(" "),
      _c("hr", { staticClass: "wp-header-end" }),
      _vm._v(" "),
      _c("ul", { staticClass: "subsubsub" }, [
        _c(
          "li",
          [
            _c(
              "router-link",
              {
                attrs: {
                  to: { name: "Vendors", query: { status: "all" } },
                  "active-class": "current",
                  exact: ""
                }
              },
              [
                _vm._v("All "),
                _c("span", { staticClass: "count" }, [
                  _vm._v("(" + _vm._s(_vm.counts.all) + ")")
                ])
              ]
            ),
            _vm._v(" | ")
          ],
          1
        ),
        _vm._v(" "),
        _c(
          "li",
          [
            _c(
              "router-link",
              {
                attrs: {
                  to: { name: "Vendors", query: { status: "approved" } },
                  "active-class": "current",
                  exact: ""
                }
              },
              [
                _vm._v("Approved "),
                _c("span", { staticClass: "count" }, [
                  _vm._v("(" + _vm._s(_vm.counts.approved) + ")")
                ])
              ]
            ),
            _vm._v(" | ")
          ],
          1
        ),
        _vm._v(" "),
        _c(
          "li",
          [
            _c(
              "router-link",
              {
                attrs: {
                  to: { name: "Vendors", query: { status: "pending" } },
                  "active-class": "current",
                  exact: ""
                }
              },
              [
                _vm._v("Pending "),
                _c("span", { staticClass: "count" }, [
                  _vm._v("(" + _vm._s(_vm.counts.pending) + ")")
                ])
              ]
            )
          ],
          1
        )
      ]),
      _vm._v(" "),
      _c("list-table", {
        attrs: {
          columns: _vm.columns,
          loading: _vm.loading,
          rows: _vm.vendors,
          actions: _vm.actions,
          actionColumn: "store_name",
          "show-cb": _vm.showCb,
          "total-items": _vm.totalItems,
          "bulk-actions": _vm.bulkActions,
          "total-pages": _vm.totalPages,
          "per-page": _vm.perPage,
          "current-page": _vm.currentPage,
          "action-column": _vm.actionColumn,
          "not-found": "No vendors found.",
          "sort-by": _vm.sortBy,
          "sort-order": _vm.sortOrder
        },
        on: {
          sort: _vm.sortCallback,
          pagination: _vm.goToPage,
          "action:click": _vm.onActionClick,
          "bulk:click": _vm.onBulkAction
        },
        scopedSlots: _vm._u([
          {
            key: "store_name",
            fn: function(data) {
              return [
                _c("img", {
                  attrs: {
                    src: data.row.gravatar,
                    alt: data.row.store_name,
                    width: "50"
                  }
                }),
                _vm._v(" "),
                _c(
                  "strong",
                  [
                    _c(
                      "router-link",
                      { attrs: { to: "/vendors/" + data.row.id } },
                      [
                        _vm._v(
                          _vm._s(
                            data.row.store_name
                              ? data.row.store_name
                              : "(no name)"
                          )
                        )
                      ]
                    )
                  ],
                  1
                )
              ]
            }
          },
          {
            key: "email",
            fn: function(data) {
              return [
                _c("a", { attrs: { href: "mailto:" + data.row.email } }, [
                  _vm._v(_vm._s(data.row.email))
                ])
              ]
            }
          },
          {
            key: "registered",
            fn: function(data) {
              return [
                _vm._v(
                  "\n            " +
                    _vm._s(
                      _vm.moment(data.row.registered).format("MMM D, YYYY")
                    ) +
                    "\n        "
                )
              ]
            }
          },
          {
            key: "enabled",
            fn: function(data) {
              return [
                _c("switches", {
                  attrs: { enabled: data.row.enabled, value: data.row.id },
                  on: { input: _vm.onSwitch }
                })
              ]
            }
          },
          {
            key: "row-actions",
            fn: function(data) {
              return _vm._l(_vm.actions, function(action, index) {
                return _c(
                  "span",
                  { class: action.key },
                  [
                    action.key == "edit"
                      ? _c("a", { attrs: { href: _vm.editUrl(data.row.id) } }, [
                          _vm._v(_vm._s(action.label))
                        ])
                      : action.key == "products"
                        ? _c(
                            "a",
                            { attrs: { href: _vm.productUrl(data.row.id) } },
                            [_vm._v(_vm._s(action.label))]
                          )
                        : action.key == "orders"
                          ? _c(
                              "a",
                              { attrs: { href: _vm.ordersUrl(data.row.id) } },
                              [_vm._v(_vm._s(action.label))]
                            )
                          : _c("a", { attrs: { href: "#" } }, [
                              _vm._v(_vm._s(action.label))
                            ]),
                    _vm._v(" "),
                    index !== _vm.actions.length - 1
                      ? [_vm._v(" | ")]
                      : _vm._e()
                  ],
                  2
                )
              })
            }
          }
        ])
      })
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-7a477aab", esExports)
  }
}

/***/ }),
/* 21 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorSingle_vue__ = __webpack_require__(5);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_849fac40_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorSingle_vue__ = __webpack_require__(23);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(22)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_VendorSingle_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_849fac40_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_VendorSingle_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "src/admin/components/VendorSingle.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-849fac40", Component.options)
  } else {
    hotAPI.reload("data-v-849fac40", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 22 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 23 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "dokan-vendor-single" },
    [
      _vm._m(0),
      _vm._v(" "),
      _vm.showDialog
        ? _c(
            "modal",
            {
              attrs: { title: "Send Email" },
              on: {
                close: function($event) {
                  _vm.showDialog = false
                }
              }
            },
            [
              _c("template", { slot: "body" }, [
                _c("div", { staticClass: "form-row" }, [
                  _c("label", { attrs: { for: "mailto" } }, [_vm._v("To")]),
                  _vm._v(" "),
                  _c("input", {
                    attrs: { type: "text", id: "mailto", disabled: "disabled" },
                    domProps: { value: _vm.mailTo }
                  })
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-row" }, [
                  _c("label", { attrs: { for: "subject" } }, [
                    _vm._v("Subject")
                  ]),
                  _vm._v(" "),
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.mail.subject,
                        expression: "mail.subject"
                      }
                    ],
                    attrs: { type: "text", id: "subject" },
                    domProps: { value: _vm.mail.subject },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.mail, "subject", $event.target.value)
                      }
                    }
                  })
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-row" }, [
                  _c("label", { attrs: { for: "message" } }, [
                    _vm._v("Message")
                  ]),
                  _vm._v(" "),
                  _c("textarea", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.mail.body,
                        expression: "mail.body"
                      }
                    ],
                    attrs: { id: "message", rows: "5", cols: "60" },
                    domProps: { value: _vm.mail.body },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.mail, "body", $event.target.value)
                      }
                    }
                  })
                ])
              ]),
              _vm._v(" "),
              _c("template", { slot: "footer" }, [
                _c(
                  "button",
                  {
                    staticClass: "button button-primary button-large",
                    on: {
                      click: function($event) {
                        _vm.sendEmail()
                      }
                    }
                  },
                  [_vm._v("Send Email")]
                )
              ])
            ],
            2
          )
        : _vm._e(),
      _vm._v(" "),
      _vm.store.id
        ? _c("div", { staticClass: "vendor-profile" }, [
            _c("section", { staticClass: "vendor-header" }, [
              _c("div", { staticClass: "profile-info" }, [
                _vm.store.featured
                  ? _c("div", { staticClass: "featured-vendor" }, [
                      _c("span", {
                        staticClass: "dashicons dashicons-star-filled",
                        attrs: { title: "Featured Vendor" }
                      })
                    ])
                  : _vm._e(),
                _vm._v(" "),
                _c("div", { staticClass: "profile-icon" }, [
                  _c("img", {
                    attrs: {
                      src: _vm.store.gravatar,
                      alt: _vm.store.store_name
                    }
                  })
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "store-info" }, [
                  _c("h2", { staticClass: "store-name" }, [
                    _vm._v(
                      _vm._s(
                        _vm.store.store_name
                          ? _vm.store.store_name
                          : "(No Name)"
                      )
                    )
                  ]),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticClass: "star-rating" },
                    _vm._l(5, function(i) {
                      return _c("span", {
                        class: [
                          "dashicons",
                          i <= _vm.store.rating.count ? "active" : ""
                        ]
                      })
                    })
                  ),
                  _vm._v(" "),
                  _c("ul", { staticClass: "store-details" }, [
                    _c("li", { staticClass: "address" }, [
                      _c("span", { staticClass: "street_1" }, [
                        _vm._v(_vm._s(_vm.store.address.street_1) + ", ")
                      ]),
                      _vm._v(" "),
                      _c("span", { staticClass: "city" }, [
                        _vm._v(_vm._s(_vm.store.address.city) + ", ")
                      ]),
                      _vm._v(" "),
                      _c("span", { staticClass: "state-zip" }, [
                        _vm._v(
                          _vm._s(_vm.store.address.state) +
                            " " +
                            _vm._s(_vm.store.address.zip)
                        )
                      ])
                    ]),
                    _vm._v(" "),
                    _c("li", { staticClass: "phone" }, [
                      _vm._v(
                        "\n                            " +
                          _vm._s(_vm.store.phone ? _vm.store.phone : "—") +
                          "\n                        "
                      )
                    ])
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "actions" }, [
                    _c(
                      "button",
                      {
                        staticClass: "button message",
                        on: {
                          click: function($event) {
                            _vm.messageDialog()
                          }
                        }
                      },
                      [
                        _c("span", {
                          staticClass: "dashicons dashicons-email"
                        }),
                        _vm._v(" Send Email")
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "button",
                      {
                        class: [
                          "button",
                          "status",
                          _vm.store.enabled ? "enabled" : "disabled"
                        ]
                      },
                      [
                        _c("span", { staticClass: "dashicons" }),
                        _vm._v(
                          " " +
                            _vm._s(_vm.store.enabled ? "Enabled" : "Disabled")
                        )
                      ]
                    )
                  ])
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "profile-banner" }, [
                _c("div", { staticClass: "banner-wrap" }, [
                  _vm.store.banner
                    ? _c("img", {
                        attrs: {
                          src: _vm.store.banner,
                          alt: _vm.store.store_name
                        }
                      })
                    : _vm._e()
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "action-links" }, [
                  _c(
                    "a",
                    {
                      staticClass: "button visit-store",
                      attrs: { href: _vm.store.shop_url, target: "_blank" }
                    },
                    [
                      _vm._v("Visit Store "),
                      _c("span", {
                        staticClass: "dashicons dashicons-arrow-right-alt"
                      })
                    ]
                  ),
                  _vm._v(" "),
                  _c(
                    "a",
                    {
                      staticClass: "button edit-store",
                      attrs: { href: _vm.editUrl() }
                    },
                    [_c("span", { staticClass: "dashicons dashicons-edit" })]
                  )
                ])
              ])
            ]),
            _vm._v(" "),
            _vm.stats !== null
              ? _c("section", { staticClass: "vendor-summary" }, [
                  _c("div", { staticClass: "summary-wrap products-revenue" }, [
                    _c("div", { staticClass: "stat-summary products" }, [
                      _c("h3", [_vm._v("Products")]),
                      _vm._v(" "),
                      _c("ul", { staticClass: "counts" }, [
                        _c("li", { staticClass: "products" }, [
                          _c("span", { staticClass: "count" }, [
                            _c("a", { attrs: { href: _vm.productUrl() } }, [
                              _vm._v(_vm._s(_vm.stats.products.total))
                            ])
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v("Total Products")
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "items" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(_vm._s(_vm.stats.products.sold))
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v("Items Sold")
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "visitors" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(_vm._s(_vm.stats.products.visitor))
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v("Store Visitors")
                          ])
                        ])
                      ])
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "stat-summary revenue" }, [
                      _c("h3", [_vm._v("Revenue")]),
                      _vm._v(" "),
                      _c("ul", { staticClass: "counts" }, [
                        _c("li", { staticClass: "orders" }, [
                          _c("span", { staticClass: "count" }, [
                            _c("a", { attrs: { href: _vm.ordersUrl() } }, [
                              _vm._v(_vm._s(_vm.stats.revenue.orders))
                            ])
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v("Orders Processed")
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "gross" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(
                              _vm._s(
                                _vm._f("currency")(_vm.stats.revenue.sales)
                              )
                            )
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v("Gross Sales")
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "earning" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(
                              _vm._s(
                                _vm._f("currency")(_vm.stats.revenue.earning)
                              )
                            )
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v("Total Earning")
                          ])
                        ])
                      ])
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "stat-summary others" }, [
                      _c("h3", [_vm._v("Others")]),
                      _vm._v(" "),
                      _c("ul", { staticClass: "counts" }, [
                        _c("li", { staticClass: "commision" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(
                              _vm._s(_vm.stats.others.commision_rate) + "%"
                            )
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v("Earning Rate")
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "balance" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(
                              _vm._s(
                                _vm._f("currency")(_vm.stats.others.balance)
                              )
                            )
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v("Current Balance")
                          ])
                        ]),
                        _vm._v(" "),
                        _c("li", { staticClass: "reviews" }, [
                          _c("span", { staticClass: "count" }, [
                            _vm._v(_vm._s(_vm.stats.others.reviews))
                          ]),
                          _vm._v(" "),
                          _c("span", { staticClass: "subhead" }, [
                            _vm._v("Reviews")
                          ])
                        ])
                      ])
                    ])
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "vendor-info" }, [
                    _c("ul", [
                      _c("li", { staticClass: "registered" }, [
                        _c("div", { staticClass: "subhead" }, [
                          _vm._v("Registered Since")
                        ]),
                        _vm._v(" "),
                        _c("span", { staticClass: "date" }, [
                          _vm._v(
                            "\n                            " +
                              _vm._s(
                                _vm
                                  .moment(_vm.store.registered)
                                  .format("MMM D, YYYY")
                              ) +
                              "\n                            (" +
                              _vm._s(
                                _vm.moment(_vm.store.registered).toNow(true)
                              ) +
                              ")\n                        "
                          )
                        ])
                      ]),
                      _vm._v(" "),
                      _c("li", { staticClass: "social-profiles" }, [
                        _c("div", { staticClass: "subhead" }, [
                          _vm._v("Social Profiles")
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "profiles" }, [
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("fb") },
                              attrs: {
                                href: _vm.store.social.fb,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-facebook-logo"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("flickr") },
                              attrs: {
                                href: _vm.store.social.flickr,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass:
                                  "flaticon-flickr-website-logo-silhouette"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("twitter") },
                              attrs: {
                                href: _vm.store.social.twitter,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-twitter-logo-silhouette"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("gplus") },
                              attrs: {
                                href: _vm.store.social.gplus,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-google-plus"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: {
                                active: _vm.isSocialActive("instagram")
                              },
                              attrs: {
                                href: _vm.store.social.instagram,
                                target: "_blank"
                              }
                            },
                            [_c("span", { staticClass: "flaticon-instagram" })]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("youtube") },
                              attrs: {
                                href: _vm.store.social.youtube,
                                target: "_blank"
                              }
                            },
                            [_c("span", { staticClass: "flaticon-youtube" })]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: { active: _vm.isSocialActive("linkedin") },
                              attrs: {
                                href: _vm.store.social.linkedin,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-linkedin-logo"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              class: {
                                active: _vm.isSocialActive("pinterest")
                              },
                              attrs: {
                                href: _vm.store.social.pinterest,
                                target: "_blank"
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "flaticon-pinterest-logo"
                              })
                            ]
                          )
                        ])
                      ]),
                      _vm._v(" "),
                      _c("li", { staticClass: "payments" }, [
                        _c("div", { staticClass: "subhead" }, [
                          _vm._v("Payment Methods")
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "payment-methods" }, [
                          _c("span", {
                            class: [
                              "flaticon-money",
                              _vm.hasPaymentEmail("paypal") ? "active" : ""
                            ],
                            attrs: { title: "PayPal Payment" }
                          }),
                          _vm._v(" "),
                          _c("span", {
                            staticClass: "flaticon-stripe-logo",
                            attrs: { title: "Stripe Connect" }
                          }),
                          _vm._v(" "),
                          _c("span", {
                            class: [
                              "flaticon-bank-building",
                              _vm.hasBank ? "active" : ""
                            ],
                            attrs: { title: "Bank Payment" }
                          }),
                          _vm._v(" "),
                          _c("span", {
                            class: [
                              "flaticon-skrill-pay-logo",
                              _vm.hasPaymentEmail("skrill") ? "active" : ""
                            ],
                            attrs: { title: "Skrill" }
                          })
                        ])
                      ]),
                      _vm._v(" "),
                      _c("li", { staticClass: "publishing" }, [
                        _c("div", { staticClass: "subhead" }, [
                          _vm._v("Product Publishing")
                        ]),
                        _vm._v(" "),
                        _vm.store.trusted
                          ? _c("span", [
                              _c("span", {
                                staticClass: "dashicons dashicons-shield"
                              }),
                              _vm._v(" Direct")
                            ])
                          : _c("span", [
                              _c("span", {
                                staticClass: "dashicons dashicons-backup"
                              }),
                              _vm._v(" Requires Review")
                            ])
                      ])
                    ])
                  ])
                ])
              : _vm._e()
          ])
        : _c("vcl-twitch", { attrs: { height: "300", primary: "#ffffff" } })
    ],
    1
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticStyle: { "margin-bottom": "10px" } }, [
      _c(
        "a",
        { staticClass: "button", attrs: { href: "javascript:history.go(-1)" } },
        [_vm._v("← Go Back")]
      )
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-849fac40", esExports)
  }
}

/***/ })
/******/ ]);