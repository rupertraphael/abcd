(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[24],{

/***/ "./resources/js/api/RolePermissionRequest.js":
/*!***************************************************!*\
  !*** ./resources/js/api/RolePermissionRequest.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_Request__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/Request */ "./resources/js/core/Request.js");
function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }



var RolePermissionRequest =
/*#__PURE__*/
function (_Request) {
  _inherits(RolePermissionRequest, _Request);

  function RolePermissionRequest() {
    _classCallCheck(this, RolePermissionRequest);

    return _possibleConstructorReturn(this, _getPrototypeOf(RolePermissionRequest).apply(this, arguments));
  }

  _createClass(RolePermissionRequest, [{
    key: "store",
    value: function store(role, permission) {
      return this.post("/api/roles/".concat(role, "/permissions/").concat(permission));
    }
  }, {
    key: "destroy",
    value: function destroy(role, permission) {
      return this["delete"]("/api/roles/".concat(role, "/permissions/").concat(permission));
    }
  }]);

  return RolePermissionRequest;
}(_core_Request__WEBPACK_IMPORTED_MODULE_0__["default"]);

/* harmony default export */ __webpack_exports__["default"] = (RolePermissionRequest);

/***/ })

}]);