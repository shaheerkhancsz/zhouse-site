(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
"use strict";

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
var _constants = require("./modules/constants");
var _connection = require("./modules/connection");
var _action = require("./modules/action.type");
var _exportTo = require("./modules/export.to.js");
var _google = require("./modules/google");
var _dropbox = require("./modules/dropbox");
var _ftp = require("./modules/ftp");
var _filters = require("./modules/filters");
var _email = require("./modules/email.js");
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = _get(_getPrototypeOf(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
function _get() { return _get = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function (e, t, r) { var p = _superPropBase(e, t); if (p) { var n = Object.getOwnPropertyDescriptor(p, t); return n.get ? n.get.call(arguments.length < 3 ? e : r) : n.value; } }, _get.apply(null, arguments); }
function _superPropBase(t, o) { for (; !{}.hasOwnProperty.call(t, o) && null !== (t = _getPrototypeOf(t));); return t; }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); } // eslint-disable-next-line no-unused-vars
/* global WPForms, wpforms_builder, wpformsEntryAutomationBuilderVars */ /**
 * Main JS file for the addon.
 *
 * @since 1.0.0
 */
var $ = jQuery;

/**
 * Class representing Entry Automation Connections within the WPForms builder.
 * Extends functionality of the base Connections class to provide automation-related features.
 *
 * @since 1.0.0
 */
var EntryAutomationConnections = /*#__PURE__*/function (_WPForms$Admin$Builde) {
  /**
   * Constructor for EntryAutomationConnections.
   *
   * @since 1.0.0
   */
  function EntryAutomationConnections() {
    var _this;
    _classCallCheck(this, EntryAutomationConnections);
    _this = _callSuper(this, EntryAutomationConnections, [jQuery(_constants.$elements.$panel), {
      entitySlug: _constants.ENTITY_SLUG
    }]);
    _this.isDropboxActivated = $('#dropbox-provider').length;
    _this.isGoogleActivated = $('#google-drive-provider').length;
    return _this;
  }

  /**
   * Document ready.
   *
   * @since 1.0.0
   */
  _inherits(EntryAutomationConnections, _WPForms$Admin$Builde);
  return _createClass(EntryAutomationConnections, [{
    key: "ready",
    value: function ready() {
      var _this2 = this;
      _superPropGet(EntryAutomationConnections, "ready", this, 3)([]);
      if (this.isReady) {
        return;
      }
      this.isReady = true;
      WPForms.Admin.Builder.Templates.add(["wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-delete"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-filter"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-schedule"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-schedule-days"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-on-duplicate-option"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-form-fields-list"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export-to-email"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export-to-ftp")]);
      (0, _connection.connectionDataLoad)().then(function (responseData) {
        return _this2.generateConnections(responseData);
      });
      if (this.isGoogleActivated) {
        (0, _google.initGoogle)();
      }
      if (this.isDropboxActivated) {
        (0, _dropbox.initDropbox)();
      }
      (0, _email.initEmail)();
    }

    /**
     * Establishes a new connection with the specified name.
     *
     * @since 1.0.0
     *
     * @param {string} name            The name of the connection to be created.
     * @param {Object} [currentValues] Optional. Values to pre-populate in the connection.
     */
  }, {
    key: "connectionCreate",
    value: function connectionCreate(name, currentValues) {
      if (!(currentValues !== null && currentValues !== void 0 && currentValues.clonedBy)) {
        (0, _connection.collapseConnections)();
      }
      (0, _connection.connectionCreate)(name, currentValues);
    }

    /**
     * Generates connections based on the provided data.
     *
     * @since 1.0.0
     *
     * @param {Object} connectionData The data used to generate connections.
     */
  }, {
    key: "connectionGenerate",
    value: function connectionGenerate(connectionData) {
      (0, _connection.connectionRender)(connectionData);
    }

    /**
     * Retrieves the default connection object with pre-defined values.
     *
     * @since 1.0.0
     *
     * @param {string} name            The name of the connection.
     * @param {Object} [currentValues] Optional. Values to pre-populate in the connection.
     *
     * @return {Object} The default connection object.
     */
  }, {
    key: "getDefaultConnection",
    value: function getDefaultConnection(name) {
      var currentValues = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      return (0, _connection.getDefaultConnection)(name, currentValues);
    }

    /**
     * Process various events as a response to UI interactions.
     *
     * @since 1.0.0
     */
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      _superPropGet(EntryAutomationConnections, "bindEvents", this, 3)([]);
      $(_constants.$elements.$tasks).on('change', _constants.selectors.actionType, _action.taskTypeHandleSwitch).on('change', _constants.selectors.exportTo, _exportTo.exportToSwitchOptions).on('click', _constants.selectors.exportTo, _exportTo.exportToShowEducationModal).on('change', _constants.selectors.fileFormat, _exportTo.onFileFormatChange).on('click', '.wpforms-entry-automation-schedule-notice .wpforms-dismiss-button', this.handleScheduleDismissButtonClick).on('change', '.wpforms-entry-automation-schedule-queue-toggle', this.handleQueueToggleChange).on('click', '.wpforms-entry-automation-ftp-connection-remove', this.removeFtpConnection).on('click', '.wpforms-entry-automation-ftp-connection-test', this.testFtpConnection);
      $(_constants.$elements.$panel).on('click', '#wpforms-entry-automation-dashboard-notice .wpforms-dismiss-button', this.handleToolsNoticeDismissButtonClick);
      $(document).on('wpformsFieldUpdate', this.handleFieldUpdated.bind(this)).on('wpformsEducationModalClose', this.handleEducationModalClose.bind(this));
    }

    /**
     * Handles the click event on the dismiss button.
     * Gets the connection ID and sets the queue_notice_display to 0.
     *
     * @since 1.0.0
     *
     * @param {Event} event The click event.
     */
  }, {
    key: "handleScheduleDismissButtonClick",
    value: function handleScheduleDismissButtonClick(event) {
      event.preventDefault();
      var $task = $(event.currentTarget).closest(_constants.selectors.task);
      $(_constants.selectors.queueNoticeDisplay, $task).val('0');
      $(event.currentTarget).closest('.wpforms-alert').fadeOut();
    }

    /**
     * Handles the click event on the dismiss notice button.
     *
     * @since 1.0.0
     *
     * @param {Event} event The click event.
     */
  }, {
    key: "handleToolsNoticeDismissButtonClick",
    value: function handleToolsNoticeDismissButtonClick(event) {
      event.preventDefault();
      var $alert = $(event.currentTarget).closest('.wpforms-alert');
      $alert.fadeOut();
      $.post(wpforms_builder.ajax_url, {
        action: 'wpforms_notice_dismiss',
        nonce: wpformsEntryAutomationBuilderVars.adminNonce,
        id: $alert.attr('id')
      });
    }

    /**
     * Handles changes to the queue toggle and updates the visibility of related settings
     * based on the toggle's state (checked or unchecked).
     *
     * @since 1.0.0
     *
     * @param {Event} e The event object containing details of the toggle change.
     */
  }, {
    key: "handleQueueToggleChange",
    value: function handleQueueToggleChange(e) {
      var $toggle = $(e.currentTarget);
      var $task = $toggle.closest(_constants.selectors.task);
      var isChecked = $toggle.is(':checked');
      $task.find('.wpforms-entry-automation-schedule-queue-settings').toggleClass('wpforms-hidden', !isChecked);
      $task.find('.wpforms-entry-automation-schedule-settings').toggleClass('wpforms-hidden', isChecked);
    }

    /**
     * Handles the update of fields within task elements by updating the cached values
     * and rendering field choices for each task.
     *
     * @since 1.0.0
     */
  }, {
    key: "handleFieldUpdated",
    value: function handleFieldUpdated() {
      var _this3 = this;
      var $tasks = this.getConnections();
      $tasks.each(function (_, task) {
        var $task = $(task);
        _this3.cacheCurrentValues($task);
        (0, _action.formFieldChoicesRender)($task);
        (0, _filters.filtersRender)($task);
      });
    }

    /**
     * Removes the FTP connection by clearing the fields associated with the connection ID.
     *
     * @since 1.0.0
     */
  }, {
    key: "removeFtpConnection",
    value: function removeFtpConnection() {
      var connectionId = $(this).data('connection-id');
      (0, _ftp.removeFtpConnection)(connectionId);
      $(this).addClass('wpforms-hidden');
    }

    /**
     * Tests the FTP connection.
     *
     * @since 1.0.0
     */
  }, {
    key: "testFtpConnection",
    value: function testFtpConnection() {
      var connectionId = $(this).data('connection-id');
      var $button = $(this);
      $button.addClass('wpforms-disabled');
      $button.append('<i class="wpforms-loading-spinner wpforms-loading-white wpforms-loading-inline"></i>');
      $(".wpforms-entry-automation-ftp-connection-remove-".concat(connectionId)).addClass('wpforms-hidden');
      (0, _ftp.testFtpConnection)(connectionId).then(function (response) {
        $button.find('.wpforms-loading-spinner').remove();
        if (response.success) {
          $(".wpforms-entry-automation-ftp-connection-remove-".concat(connectionId)).removeClass('wpforms-hidden');
        }
        (0, _ftp.displayAlert)(connectionId, response.success ? 'success' : 'failed');
        $button.removeClass('wpforms-disabled');
      }).catch(function () {
        $button.find('.wpforms-loading-spinner').remove();
        (0, _ftp.displayAlert)(connectionId, 'failed');
        $button.removeClass('wpforms-disabled');
      });
    }

    /**
     * Handles the closing of the education modal.
     * Sets the export destination to the default value when the modal is closed.
     *
     * @since 1.0.0
     *
     * @param {Event}  event           The event object triggering the modal close.
     * @param {Object} $selectedOption The jQuery object representing the selected option.
     */
  }, {
    key: "handleEducationModalClose",
    value: function handleEducationModalClose(event, $selectedOption) {
      var $select = $selectedOption.closest(_constants.selectors.exportTo);
      if (!($select !== null && $select !== void 0 && $select.length)) {
        return;
      }

      // Set the export destination to the default value.
      $($select).val('email').trigger('change');
    }

    /**
     * Processes conditional logic during the form save operation.
     * Performs specific notifications if integrated services are activated.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} connection The connection object used during the form operation.
     */
  }, {
    key: "processConditionOnFormSave",
    value: function processConditionOnFormSave(connection) {
      _superPropGet(EntryAutomationConnections, "processConditionOnFormSave", this, 3)([connection]);
      if (this.isDropboxActivated) {
        (0, _dropbox.notifyAboutRequiredConnection)(connection);
      }
      if (this.isGoogleActivated) {
        (0, _google.notifyAboutRequiredConnection)(connection);
      }
    }

    /**
     * Retrieves connection data for a given element.
     *
     * @since 1.0.0
     *
     * @param {Object} $element The element to retrieve the connection data for.
     *
     * @return {Object} An object containing the connection data.
     */
  }, {
    key: "getConnectionData",
    value: function getConnectionData($element) {
      var _superPropGet2 = _superPropGet(EntryAutomationConnections, "getConnectionData", this, 3)([$element]),
        cachedConnection = _superPropGet2.connection;
      var $task = $element.closest(_constants.selectors.task);
      // Get checked action type.
      var action = $("".concat(_constants.selectors.actionType, ":checked"), $task).val();

      // If the connection type has changed, we need to reset the connection.
      if (cachedConnection !== null && (cachedConnection === null || cachedConnection === void 0 ? void 0 : cachedConnection.action) !== action) {
        return {
          connection: this.getDefaultConnection(cachedConnection.name, {
            id: cachedConnection.id,
            action: action,
            isNew: false,
            schedule: cachedConnection.schedule
          })
        };
      }
      return {
        connection: cachedConnection
      };
    }

    /**
     * Handles the deletion of a connection by updating the cache and managing the UI state accordingly.
     *
     * @since 1.0.0
     *
     * @param {string} connectionId The ID of the connection to be deleted.
     */
  }, {
    key: "connectionDeleted",
    value: function connectionDeleted(connectionId) {
      _superPropGet(EntryAutomationConnections, "connectionDeleted", this, 3)([connectionId]);
      var connections = this.getConnections();

      // Check all connections to see if any of them have a disabled queue toggle.
      connections.each(function (index, connection) {
        var $connection = $(connection);
        var $queueToggle = $(_constants.selectors.queueToggle, $connection);

        // If the queue toggle is not checked (disabled), mark that we found a disabled toggle.
        if ($queueToggle.length && $queueToggle.is(':checked')) {
          $queueToggle.prop('checked', false).trigger('change');
          return false;
        }
      });
      if (connections.length) {
        return;
      }
      $('#wpforms-entry-automation-dashboard-notice').addClass('wpforms-hidden');
    }

    /**
     * Processes and validates the required fields for the given connection.
     *
     * @since 1.0.0
     *
     * @param {Object} connection The connection object to validate.
     */
  }, {
    key: "processRequiredFields",
    value: function processRequiredFields(connection) {
      if (!$(_constants.selectors.actionType + ':checked', connection).val()) {
        this.requiredThing = 'action_required';
        return;
      }
      _superPropGet(EntryAutomationConnections, "processRequiredFields", this, 3)([connection]);
    }
  }]);
}(WPForms.Admin.Builder.Connections);
var app = new EntryAutomationConnections();
_constants.Builder.app = app;
app.init();
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIl9jb25uZWN0aW9uIiwiX2FjdGlvbiIsIl9leHBvcnRUbyIsIl9nb29nbGUiLCJfZHJvcGJveCIsIl9mdHAiLCJfZmlsdGVycyIsIl9lbWFpbCIsIl9jbGFzc0NhbGxDaGVjayIsImEiLCJuIiwiVHlwZUVycm9yIiwiX2RlZmluZVByb3BlcnRpZXMiLCJlIiwiciIsInQiLCJsZW5ndGgiLCJvIiwiZW51bWVyYWJsZSIsImNvbmZpZ3VyYWJsZSIsIndyaXRhYmxlIiwiT2JqZWN0IiwiZGVmaW5lUHJvcGVydHkiLCJfdG9Qcm9wZXJ0eUtleSIsImtleSIsIl9jcmVhdGVDbGFzcyIsInByb3RvdHlwZSIsImkiLCJfdG9QcmltaXRpdmUiLCJfdHlwZW9mIiwiU3ltYm9sIiwidG9QcmltaXRpdmUiLCJjYWxsIiwiU3RyaW5nIiwiTnVtYmVyIiwiX2NhbGxTdXBlciIsIl9nZXRQcm90b3R5cGVPZiIsIl9wb3NzaWJsZUNvbnN0cnVjdG9yUmV0dXJuIiwiX2lzTmF0aXZlUmVmbGVjdENvbnN0cnVjdCIsIlJlZmxlY3QiLCJjb25zdHJ1Y3QiLCJjb25zdHJ1Y3RvciIsImFwcGx5IiwiX2Fzc2VydFRoaXNJbml0aWFsaXplZCIsIlJlZmVyZW5jZUVycm9yIiwiQm9vbGVhbiIsInZhbHVlT2YiLCJfc3VwZXJQcm9wR2V0IiwicCIsIl9nZXQiLCJnZXQiLCJiaW5kIiwiX3N1cGVyUHJvcEJhc2UiLCJnZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3IiLCJhcmd1bWVudHMiLCJ2YWx1ZSIsImhhc093blByb3BlcnR5Iiwic2V0UHJvdG90eXBlT2YiLCJnZXRQcm90b3R5cGVPZiIsIl9fcHJvdG9fXyIsIl9pbmhlcml0cyIsImNyZWF0ZSIsIl9zZXRQcm90b3R5cGVPZiIsIiQiLCJqUXVlcnkiLCJFbnRyeUF1dG9tYXRpb25Db25uZWN0aW9ucyIsIl9XUEZvcm1zJEFkbWluJEJ1aWxkZSIsIl90aGlzIiwiJGVsZW1lbnRzIiwiJHBhbmVsIiwiZW50aXR5U2x1ZyIsIkVOVElUWV9TTFVHIiwiaXNEcm9wYm94QWN0aXZhdGVkIiwiaXNHb29nbGVBY3RpdmF0ZWQiLCJyZWFkeSIsIl90aGlzMiIsImlzUmVhZHkiLCJXUEZvcm1zIiwiQWRtaW4iLCJCdWlsZGVyIiwiVGVtcGxhdGVzIiwiYWRkIiwiY29uY2F0IiwiY29ubmVjdGlvbkRhdGFMb2FkIiwidGhlbiIsInJlc3BvbnNlRGF0YSIsImdlbmVyYXRlQ29ubmVjdGlvbnMiLCJpbml0R29vZ2xlIiwiaW5pdERyb3Bib3giLCJpbml0RW1haWwiLCJjb25uZWN0aW9uQ3JlYXRlIiwibmFtZSIsImN1cnJlbnRWYWx1ZXMiLCJjbG9uZWRCeSIsImNvbGxhcHNlQ29ubmVjdGlvbnMiLCJjb25uZWN0aW9uR2VuZXJhdGUiLCJjb25uZWN0aW9uRGF0YSIsImNvbm5lY3Rpb25SZW5kZXIiLCJnZXREZWZhdWx0Q29ubmVjdGlvbiIsInVuZGVmaW5lZCIsImJpbmRFdmVudHMiLCIkdGFza3MiLCJvbiIsInNlbGVjdG9ycyIsImFjdGlvblR5cGUiLCJ0YXNrVHlwZUhhbmRsZVN3aXRjaCIsImV4cG9ydFRvIiwiZXhwb3J0VG9Td2l0Y2hPcHRpb25zIiwiZXhwb3J0VG9TaG93RWR1Y2F0aW9uTW9kYWwiLCJmaWxlRm9ybWF0Iiwib25GaWxlRm9ybWF0Q2hhbmdlIiwiaGFuZGxlU2NoZWR1bGVEaXNtaXNzQnV0dG9uQ2xpY2siLCJoYW5kbGVRdWV1ZVRvZ2dsZUNoYW5nZSIsInJlbW92ZUZ0cENvbm5lY3Rpb24iLCJ0ZXN0RnRwQ29ubmVjdGlvbiIsImhhbmRsZVRvb2xzTm90aWNlRGlzbWlzc0J1dHRvbkNsaWNrIiwiZG9jdW1lbnQiLCJoYW5kbGVGaWVsZFVwZGF0ZWQiLCJoYW5kbGVFZHVjYXRpb25Nb2RhbENsb3NlIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsIiR0YXNrIiwiY3VycmVudFRhcmdldCIsImNsb3Nlc3QiLCJ0YXNrIiwicXVldWVOb3RpY2VEaXNwbGF5IiwidmFsIiwiZmFkZU91dCIsIiRhbGVydCIsInBvc3QiLCJ3cGZvcm1zX2J1aWxkZXIiLCJhamF4X3VybCIsImFjdGlvbiIsIm5vbmNlIiwid3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzIiwiYWRtaW5Ob25jZSIsImlkIiwiYXR0ciIsIiR0b2dnbGUiLCJpc0NoZWNrZWQiLCJpcyIsImZpbmQiLCJ0b2dnbGVDbGFzcyIsIl90aGlzMyIsImdldENvbm5lY3Rpb25zIiwiZWFjaCIsIl8iLCJjYWNoZUN1cnJlbnRWYWx1ZXMiLCJmb3JtRmllbGRDaG9pY2VzUmVuZGVyIiwiZmlsdGVyc1JlbmRlciIsImNvbm5lY3Rpb25JZCIsImRhdGEiLCJhZGRDbGFzcyIsIiRidXR0b24iLCJhcHBlbmQiLCJyZXNwb25zZSIsInJlbW92ZSIsInN1Y2Nlc3MiLCJyZW1vdmVDbGFzcyIsImRpc3BsYXlBbGVydCIsImNhdGNoIiwiJHNlbGVjdGVkT3B0aW9uIiwiJHNlbGVjdCIsInRyaWdnZXIiLCJwcm9jZXNzQ29uZGl0aW9uT25Gb3JtU2F2ZSIsImNvbm5lY3Rpb24iLCJkcm9wYm94Tm90aWZ5QWJvdXRSZXF1aXJlZENvbm5lY3Rpb24iLCJnb29nbGVOb3RpZnlBYm91dFJlcXVpcmVkQ29ubmVjdGlvbiIsImdldENvbm5lY3Rpb25EYXRhIiwiJGVsZW1lbnQiLCJfc3VwZXJQcm9wR2V0MiIsImNhY2hlZENvbm5lY3Rpb24iLCJpc05ldyIsInNjaGVkdWxlIiwiY29ubmVjdGlvbkRlbGV0ZWQiLCJjb25uZWN0aW9ucyIsImluZGV4IiwiJGNvbm5lY3Rpb24iLCIkcXVldWVUb2dnbGUiLCJxdWV1ZVRvZ2dsZSIsInByb3AiLCJwcm9jZXNzUmVxdWlyZWRGaWVsZHMiLCJyZXF1aXJlZFRoaW5nIiwiQ29ubmVjdGlvbnMiLCJhcHAiLCJpbml0Il0sInNvdXJjZXMiOlsiZmFrZV8yYmZjZWQwNC5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tdW51c2VkLXZhcnNcbi8qIGdsb2JhbCBXUEZvcm1zLCB3cGZvcm1zX2J1aWxkZXIsIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycyAqL1xuXG4vKipcbiAqIE1haW4gSlMgZmlsZSBmb3IgdGhlIGFkZG9uLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICovXG5pbXBvcnQgeyAkZWxlbWVudHMsIEJ1aWxkZXIsIEVOVElUWV9TTFVHLCBzZWxlY3RvcnMgfSBmcm9tICcuL21vZHVsZXMvY29uc3RhbnRzJztcbmltcG9ydCB7XG5cdGNvbGxhcHNlQ29ubmVjdGlvbnMsXG5cdGNvbm5lY3Rpb25DcmVhdGUsXG5cdGNvbm5lY3Rpb25EYXRhTG9hZCxcblx0Y29ubmVjdGlvblJlbmRlciwgZ2V0RGVmYXVsdENvbm5lY3Rpb24sXG59IGZyb20gJy4vbW9kdWxlcy9jb25uZWN0aW9uJztcbmltcG9ydCB7IGZvcm1GaWVsZENob2ljZXNSZW5kZXIsIHRhc2tUeXBlSGFuZGxlU3dpdGNoIH0gZnJvbSAnLi9tb2R1bGVzL2FjdGlvbi50eXBlJztcbmltcG9ydCB7IGV4cG9ydFRvU2hvd0VkdWNhdGlvbk1vZGFsLCBleHBvcnRUb1N3aXRjaE9wdGlvbnMsIG9uRmlsZUZvcm1hdENoYW5nZSB9IGZyb20gJy4vbW9kdWxlcy9leHBvcnQudG8uanMnO1xuaW1wb3J0IHsgaW5pdEdvb2dsZSwgbm90aWZ5QWJvdXRSZXF1aXJlZENvbm5lY3Rpb24gYXMgZ29vZ2xlTm90aWZ5QWJvdXRSZXF1aXJlZENvbm5lY3Rpb24gfSBmcm9tICcuL21vZHVsZXMvZ29vZ2xlJztcbmltcG9ydCB7IGluaXREcm9wYm94LCBub3RpZnlBYm91dFJlcXVpcmVkQ29ubmVjdGlvbiBhcyBkcm9wYm94Tm90aWZ5QWJvdXRSZXF1aXJlZENvbm5lY3Rpb24gfSBmcm9tICcuL21vZHVsZXMvZHJvcGJveCc7XG5pbXBvcnQge1xuXHRkaXNwbGF5QWxlcnQsXG5cdHJlbW92ZUZ0cENvbm5lY3Rpb24sXG5cdHRlc3RGdHBDb25uZWN0aW9uLFxufSBmcm9tICcuL21vZHVsZXMvZnRwJztcbmltcG9ydCB7IGZpbHRlcnNSZW5kZXIgfSBmcm9tICcuL21vZHVsZXMvZmlsdGVycyc7XG5pbXBvcnQgeyBpbml0RW1haWwgfSBmcm9tICcuL21vZHVsZXMvZW1haWwuanMnO1xuXG5jb25zdCAkID0galF1ZXJ5O1xuXG4vKipcbiAqIENsYXNzIHJlcHJlc2VudGluZyBFbnRyeSBBdXRvbWF0aW9uIENvbm5lY3Rpb25zIHdpdGhpbiB0aGUgV1BGb3JtcyBidWlsZGVyLlxuICogRXh0ZW5kcyBmdW5jdGlvbmFsaXR5IG9mIHRoZSBiYXNlIENvbm5lY3Rpb25zIGNsYXNzIHRvIHByb3ZpZGUgYXV0b21hdGlvbi1yZWxhdGVkIGZlYXR1cmVzLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICovXG5jbGFzcyBFbnRyeUF1dG9tYXRpb25Db25uZWN0aW9ucyBleHRlbmRzIFdQRm9ybXMuQWRtaW4uQnVpbGRlci5Db25uZWN0aW9ucyB7XG5cdC8qKlxuXHQgKiBDb25zdHJ1Y3RvciBmb3IgRW50cnlBdXRvbWF0aW9uQ29ubmVjdGlvbnMuXG5cdCAqXG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKi9cblx0Y29uc3RydWN0b3IoKSB7XG5cdFx0c3VwZXIoIGpRdWVyeSggJGVsZW1lbnRzLiRwYW5lbCApLCB7IGVudGl0eVNsdWc6IEVOVElUWV9TTFVHIH0gKTtcblxuXHRcdHRoaXMuaXNEcm9wYm94QWN0aXZhdGVkID0gJCggJyNkcm9wYm94LXByb3ZpZGVyJyApLmxlbmd0aDtcblx0XHR0aGlzLmlzR29vZ2xlQWN0aXZhdGVkID0gJCggJyNnb29nbGUtZHJpdmUtcHJvdmlkZXInICkubGVuZ3RoO1xuXHR9XG5cblx0LyoqXG5cdCAqIERvY3VtZW50IHJlYWR5LlxuXHQgKlxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICovXG5cdHJlYWR5KCkge1xuXHRcdHN1cGVyLnJlYWR5KCk7XG5cblx0XHRpZiAoIHRoaXMuaXNSZWFkeSApIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cblx0XHR0aGlzLmlzUmVhZHkgPSB0cnVlO1xuXG5cdFx0V1BGb3Jtcy5BZG1pbi5CdWlsZGVyLlRlbXBsYXRlcy5hZGQoIFtcblx0XHRcdGB3cGZvcm1zLSR7IEVOVElUWV9TTFVHIH0tYnVpbGRlci1jb250ZW50LXRhc2tgLFxuXHRcdFx0YHdwZm9ybXMtJHsgRU5USVRZX1NMVUcgfS1idWlsZGVyLWNvbnRlbnQtdGFzay1leHBvcnRgLFxuXHRcdFx0YHdwZm9ybXMtJHsgRU5USVRZX1NMVUcgfS1idWlsZGVyLWNvbnRlbnQtdGFzay1kZWxldGVgLFxuXHRcdFx0YHdwZm9ybXMtJHsgRU5USVRZX1NMVUcgfS1idWlsZGVyLWNvbnRlbnQtdGFzay1maWx0ZXJgLFxuXHRcdFx0YHdwZm9ybXMtJHsgRU5USVRZX1NMVUcgfS1idWlsZGVyLWNvbnRlbnQtdGFzay1zY2hlZHVsZWAsXG5cdFx0XHRgd3Bmb3Jtcy0keyBFTlRJVFlfU0xVRyB9LWJ1aWxkZXItY29udGVudC10YXNrLXNjaGVkdWxlLWRheXNgLFxuXHRcdFx0YHdwZm9ybXMtJHsgRU5USVRZX1NMVUcgfS1idWlsZGVyLWNvbnRlbnQtdGFzay1vbi1kdXBsaWNhdGUtb3B0aW9uYCxcblx0XHRcdGB3cGZvcm1zLSR7IEVOVElUWV9TTFVHIH0tYnVpbGRlci1jb250ZW50LXRhc2stZm9ybS1maWVsZHMtbGlzdGAsXG5cdFx0XHRgd3Bmb3Jtcy0keyBFTlRJVFlfU0xVRyB9LWJ1aWxkZXItY29udGVudC10YXNrLWV4cG9ydC10by1lbWFpbGAsXG5cdFx0XHRgd3Bmb3Jtcy0keyBFTlRJVFlfU0xVRyB9LWJ1aWxkZXItY29udGVudC10YXNrLWV4cG9ydC10by1mdHBgLFxuXHRcdF0gKTtcblxuXHRcdGNvbm5lY3Rpb25EYXRhTG9hZCgpLnRoZW4oICggcmVzcG9uc2VEYXRhICkgPT4gdGhpcy5nZW5lcmF0ZUNvbm5lY3Rpb25zKCByZXNwb25zZURhdGEgKSApO1xuXG5cdFx0aWYgKCB0aGlzLmlzR29vZ2xlQWN0aXZhdGVkICkge1xuXHRcdFx0aW5pdEdvb2dsZSgpO1xuXHRcdH1cblxuXHRcdGlmICggdGhpcy5pc0Ryb3Bib3hBY3RpdmF0ZWQgKSB7XG5cdFx0XHRpbml0RHJvcGJveCgpO1xuXHRcdH1cblxuXHRcdGluaXRFbWFpbCgpO1xuXHR9XG5cblx0LyoqXG5cdCAqIEVzdGFibGlzaGVzIGEgbmV3IGNvbm5lY3Rpb24gd2l0aCB0aGUgc3BlY2lmaWVkIG5hbWUuXG5cdCAqXG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gbmFtZSAgICAgICAgICAgIFRoZSBuYW1lIG9mIHRoZSBjb25uZWN0aW9uIHRvIGJlIGNyZWF0ZWQuXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBbY3VycmVudFZhbHVlc10gT3B0aW9uYWwuIFZhbHVlcyB0byBwcmUtcG9wdWxhdGUgaW4gdGhlIGNvbm5lY3Rpb24uXG5cdCAqL1xuXHRjb25uZWN0aW9uQ3JlYXRlKCBuYW1lLCBjdXJyZW50VmFsdWVzICkge1xuXHRcdGlmICggISBjdXJyZW50VmFsdWVzPy5jbG9uZWRCeSApIHtcblx0XHRcdGNvbGxhcHNlQ29ubmVjdGlvbnMoKTtcblx0XHR9XG5cblx0XHRjb25uZWN0aW9uQ3JlYXRlKCBuYW1lLCBjdXJyZW50VmFsdWVzICk7XG5cdH1cblxuXHQvKipcblx0ICogR2VuZXJhdGVzIGNvbm5lY3Rpb25zIGJhc2VkIG9uIHRoZSBwcm92aWRlZCBkYXRhLlxuXHQgKlxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICpcblx0ICogQHBhcmFtIHtPYmplY3R9IGNvbm5lY3Rpb25EYXRhIFRoZSBkYXRhIHVzZWQgdG8gZ2VuZXJhdGUgY29ubmVjdGlvbnMuXG5cdCAqL1xuXHRjb25uZWN0aW9uR2VuZXJhdGUoIGNvbm5lY3Rpb25EYXRhICkge1xuXHRcdGNvbm5lY3Rpb25SZW5kZXIoIGNvbm5lY3Rpb25EYXRhICk7XG5cdH1cblxuXHQvKipcblx0ICogUmV0cmlldmVzIHRoZSBkZWZhdWx0IGNvbm5lY3Rpb24gb2JqZWN0IHdpdGggcHJlLWRlZmluZWQgdmFsdWVzLlxuXHQgKlxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICpcblx0ICogQHBhcmFtIHtzdHJpbmd9IG5hbWUgICAgICAgICAgICBUaGUgbmFtZSBvZiB0aGUgY29ubmVjdGlvbi5cblx0ICogQHBhcmFtIHtPYmplY3R9IFtjdXJyZW50VmFsdWVzXSBPcHRpb25hbC4gVmFsdWVzIHRvIHByZS1wb3B1bGF0ZSBpbiB0aGUgY29ubmVjdGlvbi5cblx0ICpcblx0ICogQHJldHVybiB7T2JqZWN0fSBUaGUgZGVmYXVsdCBjb25uZWN0aW9uIG9iamVjdC5cblx0ICovXG5cdGdldERlZmF1bHRDb25uZWN0aW9uKCBuYW1lLCBjdXJyZW50VmFsdWVzID0ge30gKSB7XG5cdFx0cmV0dXJuIGdldERlZmF1bHRDb25uZWN0aW9uKCBuYW1lLCBjdXJyZW50VmFsdWVzICk7XG5cdH1cblxuXHQvKipcblx0ICogUHJvY2VzcyB2YXJpb3VzIGV2ZW50cyBhcyBhIHJlc3BvbnNlIHRvIFVJIGludGVyYWN0aW9ucy5cblx0ICpcblx0ICogQHNpbmNlIDEuMC4wXG5cdCAqL1xuXHRiaW5kRXZlbnRzKCkge1xuXHRcdHN1cGVyLmJpbmRFdmVudHMoKTtcblxuXHRcdCQoICRlbGVtZW50cy4kdGFza3MgKVxuXHRcdFx0Lm9uKCAnY2hhbmdlJywgc2VsZWN0b3JzLmFjdGlvblR5cGUsIHRhc2tUeXBlSGFuZGxlU3dpdGNoIClcblx0XHRcdC5vbiggJ2NoYW5nZScsIHNlbGVjdG9ycy5leHBvcnRUbywgZXhwb3J0VG9Td2l0Y2hPcHRpb25zIClcblx0XHRcdC5vbiggJ2NsaWNrJywgc2VsZWN0b3JzLmV4cG9ydFRvLCBleHBvcnRUb1Nob3dFZHVjYXRpb25Nb2RhbCApXG5cdFx0XHQub24oICdjaGFuZ2UnLCBzZWxlY3RvcnMuZmlsZUZvcm1hdCwgb25GaWxlRm9ybWF0Q2hhbmdlIClcblx0XHRcdC5vbiggJ2NsaWNrJywgJy53cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tc2NoZWR1bGUtbm90aWNlIC53cGZvcm1zLWRpc21pc3MtYnV0dG9uJywgdGhpcy5oYW5kbGVTY2hlZHVsZURpc21pc3NCdXR0b25DbGljayApXG5cdFx0XHQub24oICdjaGFuZ2UnLCAnLndwZm9ybXMtZW50cnktYXV0b21hdGlvbi1zY2hlZHVsZS1xdWV1ZS10b2dnbGUnLCB0aGlzLmhhbmRsZVF1ZXVlVG9nZ2xlQ2hhbmdlIClcblx0XHRcdC5vbiggJ2NsaWNrJywgJy53cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tZnRwLWNvbm5lY3Rpb24tcmVtb3ZlJywgdGhpcy5yZW1vdmVGdHBDb25uZWN0aW9uIClcblx0XHRcdC5vbiggJ2NsaWNrJywgJy53cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tZnRwLWNvbm5lY3Rpb24tdGVzdCcsIHRoaXMudGVzdEZ0cENvbm5lY3Rpb24gKTtcblxuXHRcdCQoICRlbGVtZW50cy4kcGFuZWwgKVxuXHRcdFx0Lm9uKCAnY2xpY2snLCAnI3dwZm9ybXMtZW50cnktYXV0b21hdGlvbi1kYXNoYm9hcmQtbm90aWNlIC53cGZvcm1zLWRpc21pc3MtYnV0dG9uJywgdGhpcy5oYW5kbGVUb29sc05vdGljZURpc21pc3NCdXR0b25DbGljayApO1xuXG5cdFx0JCggZG9jdW1lbnQgKVxuXHRcdFx0Lm9uKCAnd3Bmb3Jtc0ZpZWxkVXBkYXRlJywgdGhpcy5oYW5kbGVGaWVsZFVwZGF0ZWQuYmluZCggdGhpcyApIClcblx0XHRcdC5vbiggJ3dwZm9ybXNFZHVjYXRpb25Nb2RhbENsb3NlJywgdGhpcy5oYW5kbGVFZHVjYXRpb25Nb2RhbENsb3NlLmJpbmQoIHRoaXMgKSApO1xuXHR9XG5cblx0LyoqXG5cdCAqIEhhbmRsZXMgdGhlIGNsaWNrIGV2ZW50IG9uIHRoZSBkaXNtaXNzIGJ1dHRvbi5cblx0ICogR2V0cyB0aGUgY29ubmVjdGlvbiBJRCBhbmQgc2V0cyB0aGUgcXVldWVfbm90aWNlX2Rpc3BsYXkgdG8gMC5cblx0ICpcblx0ICogQHNpbmNlIDEuMC4wXG5cdCAqXG5cdCAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50IFRoZSBjbGljayBldmVudC5cblx0ICovXG5cdGhhbmRsZVNjaGVkdWxlRGlzbWlzc0J1dHRvbkNsaWNrKCBldmVudCApIHtcblx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdFx0Y29uc3QgJHRhc2sgPSAkKCBldmVudC5jdXJyZW50VGFyZ2V0ICkuY2xvc2VzdCggc2VsZWN0b3JzLnRhc2sgKTtcblxuXHRcdCQoIHNlbGVjdG9ycy5xdWV1ZU5vdGljZURpc3BsYXksICR0YXNrICkudmFsKCAnMCcgKTtcblx0XHQkKCBldmVudC5jdXJyZW50VGFyZ2V0ICkuY2xvc2VzdCggJy53cGZvcm1zLWFsZXJ0JyApLmZhZGVPdXQoKTtcblx0fVxuXG5cdC8qKlxuXHQgKiBIYW5kbGVzIHRoZSBjbGljayBldmVudCBvbiB0aGUgZGlzbWlzcyBub3RpY2UgYnV0dG9uLlxuXHQgKlxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICpcblx0ICogQHBhcmFtIHtFdmVudH0gZXZlbnQgVGhlIGNsaWNrIGV2ZW50LlxuXHQgKi9cblx0aGFuZGxlVG9vbHNOb3RpY2VEaXNtaXNzQnV0dG9uQ2xpY2soIGV2ZW50ICkge1xuXHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cblx0XHRjb25zdCAkYWxlcnQgPSAkKCBldmVudC5jdXJyZW50VGFyZ2V0ICkuY2xvc2VzdCggJy53cGZvcm1zLWFsZXJ0JyApO1xuXG5cdFx0JGFsZXJ0LmZhZGVPdXQoKTtcblxuXHRcdCQucG9zdChcblx0XHRcdHdwZm9ybXNfYnVpbGRlci5hamF4X3VybCxcblx0XHRcdHtcblx0XHRcdFx0YWN0aW9uOiAnd3Bmb3Jtc19ub3RpY2VfZGlzbWlzcycsXG5cdFx0XHRcdG5vbmNlOiAgIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5hZG1pbk5vbmNlLFxuXHRcdFx0XHRpZDogXHQgJGFsZXJ0LmF0dHIoICdpZCcgKSxcblx0XHRcdH1cblx0XHQpO1xuXHR9XG5cblx0LyoqXG5cdCAqIEhhbmRsZXMgY2hhbmdlcyB0byB0aGUgcXVldWUgdG9nZ2xlIGFuZCB1cGRhdGVzIHRoZSB2aXNpYmlsaXR5IG9mIHJlbGF0ZWQgc2V0dGluZ3Ncblx0ICogYmFzZWQgb24gdGhlIHRvZ2dsZSdzIHN0YXRlIChjaGVja2VkIG9yIHVuY2hlY2tlZCkuXG5cdCAqXG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKlxuXHQgKiBAcGFyYW0ge0V2ZW50fSBlIFRoZSBldmVudCBvYmplY3QgY29udGFpbmluZyBkZXRhaWxzIG9mIHRoZSB0b2dnbGUgY2hhbmdlLlxuXHQgKi9cblx0aGFuZGxlUXVldWVUb2dnbGVDaGFuZ2UoIGUgKSB7XG5cdFx0Y29uc3QgJHRvZ2dsZSA9ICQoIGUuY3VycmVudFRhcmdldCApO1xuXHRcdGNvbnN0ICR0YXNrID0gJHRvZ2dsZS5jbG9zZXN0KCBzZWxlY3RvcnMudGFzayApO1xuXHRcdGNvbnN0IGlzQ2hlY2tlZCA9ICR0b2dnbGUuaXMoICc6Y2hlY2tlZCcgKTtcblxuXHRcdCR0YXNrLmZpbmQoICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXNjaGVkdWxlLXF1ZXVlLXNldHRpbmdzJyApLnRvZ2dsZUNsYXNzKCAnd3Bmb3Jtcy1oaWRkZW4nLCAhIGlzQ2hlY2tlZCApO1xuXHRcdCR0YXNrLmZpbmQoICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXNjaGVkdWxlLXNldHRpbmdzJyApLnRvZ2dsZUNsYXNzKCAnd3Bmb3Jtcy1oaWRkZW4nLCBpc0NoZWNrZWQgKTtcblx0fVxuXG5cdC8qKlxuXHQgKiBIYW5kbGVzIHRoZSB1cGRhdGUgb2YgZmllbGRzIHdpdGhpbiB0YXNrIGVsZW1lbnRzIGJ5IHVwZGF0aW5nIHRoZSBjYWNoZWQgdmFsdWVzXG5cdCAqIGFuZCByZW5kZXJpbmcgZmllbGQgY2hvaWNlcyBmb3IgZWFjaCB0YXNrLlxuXHQgKlxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICovXG5cdGhhbmRsZUZpZWxkVXBkYXRlZCgpIHtcblx0XHRjb25zdCAkdGFza3MgPSB0aGlzLmdldENvbm5lY3Rpb25zKCk7XG5cblx0XHQkdGFza3MuZWFjaCggKCBfLCB0YXNrICkgPT4ge1xuXHRcdFx0Y29uc3QgJHRhc2sgPSAkKCB0YXNrICk7XG5cblx0XHRcdHRoaXMuY2FjaGVDdXJyZW50VmFsdWVzKCAkdGFzayApO1xuXHRcdFx0Zm9ybUZpZWxkQ2hvaWNlc1JlbmRlciggJHRhc2sgKTtcblx0XHRcdGZpbHRlcnNSZW5kZXIoICR0YXNrICk7XG5cdFx0fSApO1xuXHR9XG5cblx0LyoqXG5cdCAqIFJlbW92ZXMgdGhlIEZUUCBjb25uZWN0aW9uIGJ5IGNsZWFyaW5nIHRoZSBmaWVsZHMgYXNzb2NpYXRlZCB3aXRoIHRoZSBjb25uZWN0aW9uIElELlxuXHQgKlxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICovXG5cdHJlbW92ZUZ0cENvbm5lY3Rpb24oKSB7XG5cdFx0Y29uc3QgY29ubmVjdGlvbklkID0gJCggdGhpcyApLmRhdGEoICdjb25uZWN0aW9uLWlkJyApO1xuXG5cdFx0cmVtb3ZlRnRwQ29ubmVjdGlvbiggY29ubmVjdGlvbklkICk7XG5cblx0XHQkKCB0aGlzICkuYWRkQ2xhc3MoICd3cGZvcm1zLWhpZGRlbicgKTtcblx0fVxuXG5cdC8qKlxuXHQgKiBUZXN0cyB0aGUgRlRQIGNvbm5lY3Rpb24uXG5cdCAqXG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKi9cblx0dGVzdEZ0cENvbm5lY3Rpb24oKSB7XG5cdFx0Y29uc3QgY29ubmVjdGlvbklkID0gJCggdGhpcyApLmRhdGEoICdjb25uZWN0aW9uLWlkJyApO1xuXG5cdFx0Y29uc3QgJGJ1dHRvbiA9ICQoIHRoaXMgKTtcblx0XHQkYnV0dG9uLmFkZENsYXNzKCAnd3Bmb3Jtcy1kaXNhYmxlZCcgKTtcblx0XHQkYnV0dG9uLmFwcGVuZCggJzxpIGNsYXNzPVwid3Bmb3Jtcy1sb2FkaW5nLXNwaW5uZXIgd3Bmb3Jtcy1sb2FkaW5nLXdoaXRlIHdwZm9ybXMtbG9hZGluZy1pbmxpbmVcIj48L2k+JyApO1xuXHRcdCQoIGAud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLWZ0cC1jb25uZWN0aW9uLXJlbW92ZS0keyBjb25uZWN0aW9uSWQgfWAgKS5hZGRDbGFzcyggJ3dwZm9ybXMtaGlkZGVuJyApO1xuXG5cdFx0dGVzdEZ0cENvbm5lY3Rpb24oIGNvbm5lY3Rpb25JZCApLnRoZW4oICggcmVzcG9uc2UgKSA9PiB7XG5cdFx0XHQkYnV0dG9uLmZpbmQoICcud3Bmb3Jtcy1sb2FkaW5nLXNwaW5uZXInICkucmVtb3ZlKCk7XG5cblx0XHRcdGlmICggcmVzcG9uc2Uuc3VjY2VzcyApIHtcblx0XHRcdFx0JCggYC53cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tZnRwLWNvbm5lY3Rpb24tcmVtb3ZlLSR7IGNvbm5lY3Rpb25JZCB9YCApLnJlbW92ZUNsYXNzKCAnd3Bmb3Jtcy1oaWRkZW4nICk7XG5cdFx0XHR9XG5cblx0XHRcdGRpc3BsYXlBbGVydCggY29ubmVjdGlvbklkLCByZXNwb25zZS5zdWNjZXNzID8gJ3N1Y2Nlc3MnIDogJ2ZhaWxlZCcgKTtcblxuXHRcdFx0JGJ1dHRvbi5yZW1vdmVDbGFzcyggJ3dwZm9ybXMtZGlzYWJsZWQnICk7XG5cdFx0fSApLmNhdGNoKCAoKSA9PiB7XG5cdFx0XHQkYnV0dG9uLmZpbmQoICcud3Bmb3Jtcy1sb2FkaW5nLXNwaW5uZXInICkucmVtb3ZlKCk7XG5cdFx0XHRkaXNwbGF5QWxlcnQoIGNvbm5lY3Rpb25JZCwgJ2ZhaWxlZCcgKTtcblx0XHRcdCRidXR0b24ucmVtb3ZlQ2xhc3MoICd3cGZvcm1zLWRpc2FibGVkJyApO1xuXHRcdH0gKTtcblx0fVxuXG5cdC8qKlxuXHQgKiBIYW5kbGVzIHRoZSBjbG9zaW5nIG9mIHRoZSBlZHVjYXRpb24gbW9kYWwuXG5cdCAqIFNldHMgdGhlIGV4cG9ydCBkZXN0aW5hdGlvbiB0byB0aGUgZGVmYXVsdCB2YWx1ZSB3aGVuIHRoZSBtb2RhbCBpcyBjbG9zZWQuXG5cdCAqXG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKlxuXHQgKiBAcGFyYW0ge0V2ZW50fSAgZXZlbnQgICAgICAgICAgIFRoZSBldmVudCBvYmplY3QgdHJpZ2dlcmluZyB0aGUgbW9kYWwgY2xvc2UuXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSAkc2VsZWN0ZWRPcHRpb24gVGhlIGpRdWVyeSBvYmplY3QgcmVwcmVzZW50aW5nIHRoZSBzZWxlY3RlZCBvcHRpb24uXG5cdCAqL1xuXHRoYW5kbGVFZHVjYXRpb25Nb2RhbENsb3NlKCBldmVudCwgJHNlbGVjdGVkT3B0aW9uICkge1xuXHRcdGNvbnN0ICRzZWxlY3QgPSAkc2VsZWN0ZWRPcHRpb24uY2xvc2VzdCggc2VsZWN0b3JzLmV4cG9ydFRvICk7XG5cblx0XHRpZiAoICEgJHNlbGVjdD8ubGVuZ3RoICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdC8vIFNldCB0aGUgZXhwb3J0IGRlc3RpbmF0aW9uIHRvIHRoZSBkZWZhdWx0IHZhbHVlLlxuXHRcdCQoICRzZWxlY3QgKS52YWwoICdlbWFpbCcgKS50cmlnZ2VyKCAnY2hhbmdlJyApO1xuXHR9XG5cblx0LyoqXG5cdCAqIFByb2Nlc3NlcyBjb25kaXRpb25hbCBsb2dpYyBkdXJpbmcgdGhlIGZvcm0gc2F2ZSBvcGVyYXRpb24uXG5cdCAqIFBlcmZvcm1zIHNwZWNpZmljIG5vdGlmaWNhdGlvbnMgaWYgaW50ZWdyYXRlZCBzZXJ2aWNlcyBhcmUgYWN0aXZhdGVkLlxuXHQgKlxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICpcblx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gY29ubmVjdGlvbiBUaGUgY29ubmVjdGlvbiBvYmplY3QgdXNlZCBkdXJpbmcgdGhlIGZvcm0gb3BlcmF0aW9uLlxuXHQgKi9cblx0cHJvY2Vzc0NvbmRpdGlvbk9uRm9ybVNhdmUoIGNvbm5lY3Rpb24gKSB7XG5cdFx0c3VwZXIucHJvY2Vzc0NvbmRpdGlvbk9uRm9ybVNhdmUoIGNvbm5lY3Rpb24gKTtcblxuXHRcdGlmICggdGhpcy5pc0Ryb3Bib3hBY3RpdmF0ZWQgKSB7XG5cdFx0XHRkcm9wYm94Tm90aWZ5QWJvdXRSZXF1aXJlZENvbm5lY3Rpb24oIGNvbm5lY3Rpb24gKTtcblx0XHR9XG5cblx0XHRpZiAoIHRoaXMuaXNHb29nbGVBY3RpdmF0ZWQgKSB7XG5cdFx0XHRnb29nbGVOb3RpZnlBYm91dFJlcXVpcmVkQ29ubmVjdGlvbiggY29ubmVjdGlvbiApO1xuXHRcdH1cblx0fVxuXG5cdC8qKlxuXHQgKiBSZXRyaWV2ZXMgY29ubmVjdGlvbiBkYXRhIGZvciBhIGdpdmVuIGVsZW1lbnQuXG5cdCAqXG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gJGVsZW1lbnQgVGhlIGVsZW1lbnQgdG8gcmV0cmlldmUgdGhlIGNvbm5lY3Rpb24gZGF0YSBmb3IuXG5cdCAqXG5cdCAqIEByZXR1cm4ge09iamVjdH0gQW4gb2JqZWN0IGNvbnRhaW5pbmcgdGhlIGNvbm5lY3Rpb24gZGF0YS5cblx0ICovXG5cdGdldENvbm5lY3Rpb25EYXRhKCAkZWxlbWVudCApIHtcblx0XHRjb25zdCB7IGNvbm5lY3Rpb246IGNhY2hlZENvbm5lY3Rpb24gfSA9IHN1cGVyLmdldENvbm5lY3Rpb25EYXRhKCAkZWxlbWVudCApO1xuXG5cdFx0Y29uc3QgJHRhc2sgPSAkZWxlbWVudC5jbG9zZXN0KCBzZWxlY3RvcnMudGFzayApO1xuXHRcdC8vIEdldCBjaGVja2VkIGFjdGlvbiB0eXBlLlxuXHRcdGNvbnN0IGFjdGlvbiA9ICQoIGAkeyBzZWxlY3RvcnMuYWN0aW9uVHlwZSB9OmNoZWNrZWRgLCAkdGFzayApLnZhbCgpO1xuXG5cdFx0Ly8gSWYgdGhlIGNvbm5lY3Rpb24gdHlwZSBoYXMgY2hhbmdlZCwgd2UgbmVlZCB0byByZXNldCB0aGUgY29ubmVjdGlvbi5cblx0XHRpZiAoIGNhY2hlZENvbm5lY3Rpb24gIT09IG51bGwgJiYgY2FjaGVkQ29ubmVjdGlvbj8uYWN0aW9uICE9PSBhY3Rpb24gKSB7XG5cdFx0XHRyZXR1cm4ge1xuXHRcdFx0XHRjb25uZWN0aW9uOiB0aGlzLmdldERlZmF1bHRDb25uZWN0aW9uKCBjYWNoZWRDb25uZWN0aW9uLm5hbWUsIHsgaWQ6IGNhY2hlZENvbm5lY3Rpb24uaWQsIGFjdGlvbiwgaXNOZXc6IGZhbHNlLCBzY2hlZHVsZTogY2FjaGVkQ29ubmVjdGlvbi5zY2hlZHVsZSB9ICksXG5cdFx0XHR9O1xuXHRcdH1cblxuXHRcdHJldHVybiB7XG5cdFx0XHRjb25uZWN0aW9uOiBjYWNoZWRDb25uZWN0aW9uLFxuXHRcdH07XG5cdH1cblxuXHQvKipcblx0ICogSGFuZGxlcyB0aGUgZGVsZXRpb24gb2YgYSBjb25uZWN0aW9uIGJ5IHVwZGF0aW5nIHRoZSBjYWNoZSBhbmQgbWFuYWdpbmcgdGhlIFVJIHN0YXRlIGFjY29yZGluZ2x5LlxuXHQgKlxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICpcblx0ICogQHBhcmFtIHtzdHJpbmd9IGNvbm5lY3Rpb25JZCBUaGUgSUQgb2YgdGhlIGNvbm5lY3Rpb24gdG8gYmUgZGVsZXRlZC5cblx0ICovXG5cdGNvbm5lY3Rpb25EZWxldGVkKCBjb25uZWN0aW9uSWQgKSB7XG5cdFx0c3VwZXIuY29ubmVjdGlvbkRlbGV0ZWQoIGNvbm5lY3Rpb25JZCApO1xuXG5cdFx0Y29uc3QgY29ubmVjdGlvbnMgPSB0aGlzLmdldENvbm5lY3Rpb25zKCk7XG5cblx0XHQvLyBDaGVjayBhbGwgY29ubmVjdGlvbnMgdG8gc2VlIGlmIGFueSBvZiB0aGVtIGhhdmUgYSBkaXNhYmxlZCBxdWV1ZSB0b2dnbGUuXG5cdFx0Y29ubmVjdGlvbnMuZWFjaCggKCBpbmRleCwgY29ubmVjdGlvbiApID0+IHtcblx0XHRcdGNvbnN0ICRjb25uZWN0aW9uID0gJCggY29ubmVjdGlvbiApO1xuXHRcdFx0Y29uc3QgJHF1ZXVlVG9nZ2xlID0gJCggc2VsZWN0b3JzLnF1ZXVlVG9nZ2xlLCAkY29ubmVjdGlvbiApO1xuXG5cdFx0XHQvLyBJZiB0aGUgcXVldWUgdG9nZ2xlIGlzIG5vdCBjaGVja2VkIChkaXNhYmxlZCksIG1hcmsgdGhhdCB3ZSBmb3VuZCBhIGRpc2FibGVkIHRvZ2dsZS5cblx0XHRcdGlmICggJHF1ZXVlVG9nZ2xlLmxlbmd0aCAmJiAkcXVldWVUb2dnbGUuaXMoICc6Y2hlY2tlZCcgKSApIHtcblx0XHRcdFx0JHF1ZXVlVG9nZ2xlLnByb3AoICdjaGVja2VkJywgZmFsc2UgKS50cmlnZ2VyKCAnY2hhbmdlJyApO1xuXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH1cblx0XHR9ICk7XG5cblx0XHRpZiAoIGNvbm5lY3Rpb25zLmxlbmd0aCApIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cblx0XHQkKCAnI3dwZm9ybXMtZW50cnktYXV0b21hdGlvbi1kYXNoYm9hcmQtbm90aWNlJyApLmFkZENsYXNzKCAnd3Bmb3Jtcy1oaWRkZW4nICk7XG5cdH1cblxuXHQvKipcblx0ICogUHJvY2Vzc2VzIGFuZCB2YWxpZGF0ZXMgdGhlIHJlcXVpcmVkIGZpZWxkcyBmb3IgdGhlIGdpdmVuIGNvbm5lY3Rpb24uXG5cdCAqXG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gY29ubmVjdGlvbiBUaGUgY29ubmVjdGlvbiBvYmplY3QgdG8gdmFsaWRhdGUuXG5cdCAqL1xuXHRwcm9jZXNzUmVxdWlyZWRGaWVsZHMoIGNvbm5lY3Rpb24gKSB7XG5cdFx0aWYgKCAhICQoIHNlbGVjdG9ycy5hY3Rpb25UeXBlICsgJzpjaGVja2VkJywgY29ubmVjdGlvbiApLnZhbCgpICkge1xuXHRcdFx0dGhpcy5yZXF1aXJlZFRoaW5nID0gJ2FjdGlvbl9yZXF1aXJlZCc7XG5cblx0XHRcdHJldHVybjtcblx0XHR9XG5cblx0XHRzdXBlci5wcm9jZXNzUmVxdWlyZWRGaWVsZHMoIGNvbm5lY3Rpb24gKTtcblx0fVxufVxuXG5jb25zdCBhcHAgPSBuZXcgRW50cnlBdXRvbWF0aW9uQ29ubmVjdGlvbnMoKTtcbkJ1aWxkZXIuYXBwID0gYXBwO1xuYXBwLmluaXQoKTtcbiJdLCJtYXBwaW5ncyI6Ijs7O0FBUUEsSUFBQUEsVUFBQSxHQUFBQyxPQUFBO0FBQ0EsSUFBQUMsV0FBQSxHQUFBRCxPQUFBO0FBTUEsSUFBQUUsT0FBQSxHQUFBRixPQUFBO0FBQ0EsSUFB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
},{"./modules/action.type":2,"./modules/connection":3,"./modules/constants":4,"./modules/dropbox":6,"./modules/email.js":7,"./modules/export.to.js":8,"./modules/filters":9,"./modules/ftp":10,"./modules/google":11}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.exportTypeOnSwitch = exports.deleteTypeOnSwitch = void 0;
exports.formFieldChoicesRender = formFieldChoicesRender;
exports.taskTypeHandleSwitch = void 0;
var _constants = require("./constants");
var _filters = require("./filters");
var _schedule = require("./schedule");
var _exportTo = require("./export.to.js");
var _onDuplicateOption = require("./on.duplicate.option.js");
/* global wpf, WPForms, WPFormsChoicesList, wpformsEntryAutomationBuilderVars */

/**
 * @param wpformsEntryAutomationBuilderVars.exportDestinations.educationAddons
 */

/**
 * Task-type-related functions for Entry Automation.
 *
 * @since 1.0.0
 */

var $ = jQuery;

/**
 * Renders form field choices for a specific task in the automation builder interface.
 * This function retrieves the necessary form fields and loads a template for displaying
 * those fields in the automation task options container.
 *
 * @since 1.0.0
 *
 * @param {Object} $task A jQuery object representing the task container where the form fields are rendered.
 */
function formFieldChoicesRender($task) {
  // Load the specific template from Templates.
  var filterTemplate = _constants.Templates.get("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-form-fields-list"));
  var formFields = wpf.getFields(false, true, true);
  var _Builder$app$getConne = _constants.Builder.app.getConnectionData($task),
    connection = _Builder$app$getConne.connection;

  // If the template exists, insert the generated HTML into the options' container.
  $(_constants.selectors.formFieldsWrapper, $task).html(filterTemplate({
    connection: connection,
    formFields: formFields,
    provider: _constants.ENTITY_SLUG
  }));

  // Initialize checkbox selection for all choice lists.
  $('.wpforms-choices-list', $task).each(function (index, element) {
    WPFormsChoicesList.init($(element));
  });
}

/**
 * Handles the switch of tasks and initializes various UI components associated with the task.
 *
 * @since 1.0.0
 *
 * @param {Object} $task The task element to initialize and re-render components for.
 */
var exportTypeOnSwitch = exports.exportTypeOnSwitch = function exportTypeOnSwitch($task) {
  // Trigger the "Export To" option to render destination templates
  // dynamically based on the selected value.
  _exportTo.exportToSwitchOptions.call($(_constants.selectors.exportTo, $task));

  // Render choices with form fields.
  formFieldChoicesRender($task);
  (0, _filters.filtersRender)($task);
  (0, _schedule.scheduleRender)($task);
  (0, _onDuplicateOption.onDuplicateRender)($task);

  // Init Smart Tags widget in custom fields.
  WPForms.Admin.Builder.SmartTags.initWidgets($task);
  wpf.initTooltips($task);
};

/**
 * Handles the actions to perform when a task's status is switched,
 * rendering the related filters and schedule.
 *
 * @since 1.0.0
 *
 * @param {Object} $task The task object that is being switched.
 */
var deleteTypeOnSwitch = exports.deleteTypeOnSwitch = function deleteTypeOnSwitch($task) {
  (0, _filters.filtersRender)($task);
  (0, _schedule.scheduleRender)($task);
  wpf.initTooltips($task);
};

/**
 * Handles the task type switching within a given task context.
 * Updates the option container with the specific template content based on the selected task type.
 * Initializes additional widget components and checkbox selection.
 *
 * @since 1.0.0
 */
var taskTypeHandleSwitch = exports.taskTypeHandleSwitch = function taskTypeHandleSwitch() {
  var _handlers$taskType;
  var $this = $(this);
  var $task = $this.closest(_constants.selectors.task);
  $(_constants.selectors.typeOptions, $task).html('');

  // Retrieve the task type from the hidden input field within the clicked choice.
  var taskType = $this.is(':checked') && $this.val();

  // Check if a task type is defined.
  if (!taskType) {
    return;
  }

  // Load the specific template from Templates.
  var tmplTaskType = _constants.Templates.get("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-").concat(taskType));

  // If the template exists, insert the generated HTML into the options' container.
  $(_constants.selectors.typeOptions, $task).html(tmplTaskType({
    connection: _constants.Builder.app.getConnectionData($task).connection,
    additionalFields: wpformsEntryAutomationBuilderVars.additionalFields,
    fileFormats: wpformsEntryAutomationBuilderVars.fileFormats,
    exportDestinations: wpformsEntryAutomationBuilderVars.exportDestinations.labels,
    deliveryAddons: wpformsEntryAutomationBuilderVars.exportDestinations.educationAddons,
    activatedAddons: wpformsEntryAutomationBuilderVars.exportDestinations.activatedAddons,
    entriesNumberOptions: wpformsEntryAutomationBuilderVars.entriesNumberOptions,
    provider: _constants.ENTITY_SLUG
  }));
  var handlers = {
    export: exportTypeOnSwitch,
    delete: deleteTypeOnSwitch
  };
  (_handlers$taskType = handlers[taskType]) === null || _handlers$taskType === void 0 || _handlers$taskType.call(handlers, $task);
};
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIl9maWx0ZXJzIiwiX3NjaGVkdWxlIiwiX2V4cG9ydFRvIiwiX29uRHVwbGljYXRlT3B0aW9uIiwiJCIsImpRdWVyeSIsImZvcm1GaWVsZENob2ljZXNSZW5kZXIiLCIkdGFzayIsImZpbHRlclRlbXBsYXRlIiwiVGVtcGxhdGVzIiwiZ2V0IiwiY29uY2F0IiwiRU5USVRZX1NMVUciLCJmb3JtRmllbGRzIiwid3BmIiwiZ2V0RmllbGRzIiwiX0J1aWxkZXIkYXBwJGdldENvbm5lIiwiQnVpbGRlciIsImFwcCIsImdldENvbm5lY3Rpb25EYXRhIiwiY29ubmVjdGlvbiIsInNlbGVjdG9ycyIsImZvcm1GaWVsZHNXcmFwcGVyIiwiaHRtbCIsInByb3ZpZGVyIiwiZWFjaCIsImluZGV4IiwiZWxlbWVudCIsIldQRm9ybXNDaG9pY2VzTGlzdCIsImluaXQiLCJleHBvcnRUeXBlT25Td2l0Y2giLCJleHBvcnRzIiwiZXhwb3J0VG9Td2l0Y2hPcHRpb25zIiwiY2FsbCIsImV4cG9ydFRvIiwiZmlsdGVyc1JlbmRlciIsInNjaGVkdWxlUmVuZGVyIiwib25EdXBsaWNhdGVSZW5kZXIiLCJXUEZvcm1zIiwiQWRtaW4iLCJTbWFydFRhZ3MiLCJpbml0V2lkZ2V0cyIsImluaXRUb29sdGlwcyIsImRlbGV0ZVR5cGVPblN3aXRjaCIsInRhc2tUeXBlSGFuZGxlU3dpdGNoIiwiX2hhbmRsZXJzJHRhc2tUeXBlIiwiJHRoaXMiLCJjbG9zZXN0IiwidGFzayIsInR5cGVPcHRpb25zIiwidGFza1R5cGUiLCJpcyIsInZhbCIsInRtcGxUYXNrVHlwZSIsImFkZGl0aW9uYWxGaWVsZHMiLCJ3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMiLCJmaWxlRm9ybWF0cyIsImV4cG9ydERlc3RpbmF0aW9ucyIsImxhYmVscyIsImRlbGl2ZXJ5QWRkb25zIiwiZWR1Y2F0aW9uQWRkb25zIiwiYWN0aXZhdGVkQWRkb25zIiwiZW50cmllc051bWJlck9wdGlvbnMiLCJoYW5kbGVycyIsImV4cG9ydCIsImRlbGV0ZSJdLCJzb3VyY2VzIjpbImFjdGlvbi50eXBlLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCB3cGYsIFdQRm9ybXMsIFdQRm9ybXNDaG9pY2VzTGlzdCwgd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzICovXG5cbi8qKlxuICogQHBhcmFtIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5leHBvcnREZXN0aW5hdGlvbnMuZWR1Y2F0aW9uQWRkb25zXG4gKi9cblxuLyoqXG4gKiBUYXNrLXR5cGUtcmVsYXRlZCBmdW5jdGlvbnMgZm9yIEVudHJ5IEF1dG9tYXRpb24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmltcG9ydCB7IFRlbXBsYXRlcywgc2VsZWN0b3JzLCBFTlRJVFlfU0xVRywgQnVpbGRlciB9IGZyb20gJy4vY29uc3RhbnRzJztcbmltcG9ydCB7IGZpbHRlcnNSZW5kZXIgfSBmcm9tICcuL2ZpbHRlcnMnO1xuaW1wb3J0IHsgc2NoZWR1bGVSZW5kZXIgfSBmcm9tICcuL3NjaGVkdWxlJztcbmltcG9ydCB7IGV4cG9ydFRvU3dpdGNoT3B0aW9ucyB9IGZyb20gJy4vZXhwb3J0LnRvLmpzJztcbmltcG9ydCB7IG9uRHVwbGljYXRlUmVuZGVyIH0gZnJvbSAnLi9vbi5kdXBsaWNhdGUub3B0aW9uLmpzJztcblxuY29uc3QgJCA9IGpRdWVyeTtcblxuLyoqXG4gKiBSZW5kZXJzIGZvcm0gZmllbGQgY2hvaWNlcyBmb3IgYSBzcGVjaWZpYyB0YXNrIGluIHRoZSBhdXRvbWF0aW9uIGJ1aWxkZXIgaW50ZXJmYWNlLlxuICogVGhpcyBmdW5jdGlvbiByZXRyaWV2ZXMgdGhlIG5lY2Vzc2FyeSBmb3JtIGZpZWxkcyBhbmQgbG9hZHMgYSB0ZW1wbGF0ZSBmb3IgZGlzcGxheWluZ1xuICogdGhvc2UgZmllbGRzIGluIHRoZSBhdXRvbWF0aW9uIHRhc2sgb3B0aW9ucyBjb250YWluZXIuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtPYmplY3R9ICR0YXNrIEEgalF1ZXJ5IG9iamVjdCByZXByZXNlbnRpbmcgdGhlIHRhc2sgY29udGFpbmVyIHdoZXJlIHRoZSBmb3JtIGZpZWxkcyBhcmUgcmVuZGVyZWQuXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBmb3JtRmllbGRDaG9pY2VzUmVuZGVyKCAkdGFzayApIHtcblx0Ly8gTG9hZCB0aGUgc3BlY2lmaWMgdGVtcGxhdGUgZnJvbSBUZW1wbGF0ZXMuXG5cdGNvbnN0IGZpbHRlclRlbXBsYXRlID0gVGVtcGxhdGVzLmdldCggYHdwZm9ybXMtJHsgRU5USVRZX1NMVUcgfS1idWlsZGVyLWNvbnRlbnQtdGFzay1mb3JtLWZpZWxkcy1saXN0YCApO1xuXHRjb25zdCBmb3JtRmllbGRzID0gd3BmLmdldEZpZWxkcyggZmFsc2UsIHRydWUsIHRydWUgKTtcblx0Y29uc3QgeyBjb25uZWN0aW9uIH0gPSBCdWlsZGVyLmFwcC5nZXRDb25uZWN0aW9uRGF0YSggJHRhc2sgKTtcblxuXHQvLyBJZiB0aGUgdGVtcGxhdGUgZXhpc3RzLCBpbnNlcnQgdGhlIGdlbmVyYXRlZCBIVE1MIGludG8gdGhlIG9wdGlvbnMnIGNvbnRhaW5lci5cblx0JCggc2VsZWN0b3JzLmZvcm1GaWVsZHNXcmFwcGVyLCAkdGFzayApLmh0bWwoIGZpbHRlclRlbXBsYXRlKCB7XG5cdFx0Y29ubmVjdGlvbixcblx0XHRmb3JtRmllbGRzLFxuXHRcdHByb3ZpZGVyOiBFTlRJVFlfU0xVRyxcblx0fSApICk7XG5cblx0Ly8gSW5pdGlhbGl6ZSBjaGVja2JveCBzZWxlY3Rpb24gZm9yIGFsbCBjaG9pY2UgbGlzdHMuXG5cdCQoICcud3Bmb3Jtcy1jaG9pY2VzLWxpc3QnLCAkdGFzayApLmVhY2goICggaW5kZXgsIGVsZW1lbnQgKSA9PiB7XG5cdFx0V1BGb3Jtc0Nob2ljZXNMaXN0LmluaXQoICQoIGVsZW1lbnQgKSApO1xuXHR9ICk7XG59XG5cbi8qKlxuICogSGFuZGxlcyB0aGUgc3dpdGNoIG9mIHRhc2tzIGFuZCBpbml0aWFsaXplcyB2YXJpb3VzIFVJIGNvbXBvbmVudHMgYXNzb2NpYXRlZCB3aXRoIHRoZSB0YXNrLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSAkdGFzayBUaGUgdGFzayBlbGVtZW50IHRvIGluaXRpYWxpemUgYW5kIHJlLXJlbmRlciBjb21wb25lbnRzIGZvci5cbiAqL1xuZXhwb3J0IGNvbnN0IGV4cG9ydFR5cGVPblN3aXRjaCA9IGZ1bmN0aW9uKCAkdGFzayApIHtcblx0Ly8gVHJpZ2dlciB0aGUgXCJFeHBvcnQgVG9cIiBvcHRpb24gdG8gcmVuZGVyIGRlc3RpbmF0aW9uIHRlbXBsYXRlc1xuXHQvLyBkeW5hbWljYWxseSBiYXNlZCBvbiB0aGUgc2VsZWN0ZWQgdmFsdWUuXG5cdGV4cG9ydFRvU3dpdGNoT3B0aW9ucy5jYWxsKCAkKCBzZWxlY3RvcnMuZXhwb3J0VG8sICR0YXNrICkgKTtcblxuXHQvLyBSZW5kZXIgY2hvaWNlcyB3aXRoIGZvcm0gZmllbGRzLlxuXHRmb3JtRmllbGRDaG9pY2VzUmVuZGVyKCAkdGFzayApO1xuXG5cdGZpbHRlcnNSZW5kZXIoICR0YXNrICk7XG5cdHNjaGVkdWxlUmVuZGVyKCAkdGFzayApO1xuXHRvbkR1cGxpY2F0ZVJlbmRlciggJHRhc2sgKTtcblxuXHQvLyBJbml0IFNtYXJ0IFRhZ3Mgd2lkZ2V0IGluIGN1c3RvbSBmaWVsZHMuXG5cdFdQRm9ybXMuQWRtaW4uQnVpbGRlci5TbWFydFRhZ3MuaW5pdFdpZGdldHMoICR0YXNrICk7XG5cdHdwZi5pbml0VG9vbHRpcHMoICR0YXNrICk7XG59O1xuXG4vKipcbiAqIEhhbmRsZXMgdGhlIGFjdGlvbnMgdG8gcGVyZm9ybSB3aGVuIGEgdGFzaydzIHN0YXR1cyBpcyBzd2l0Y2hlZCxcbiAqIHJlbmRlcmluZyB0aGUgcmVsYXRlZCBmaWx0ZXJzIGFuZCBzY2hlZHVsZS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gJHRhc2sgVGhlIHRhc2sgb2JqZWN0IHRoYXQgaXMgYmVpbmcgc3dpdGNoZWQuXG4gKi9cbmV4cG9ydCBjb25zdCBkZWxldGVUeXBlT25Td2l0Y2ggPSBmdW5jdGlvbiggJHRhc2sgKSB7XG5cdGZpbHRlcnNSZW5kZXIoICR0YXNrICk7XG5cdHNjaGVkdWxlUmVuZGVyKCAkdGFzayApO1xuXG5cdHdwZi5pbml0VG9vbHRpcHMoICR0YXNrICk7XG59O1xuXG4vKipcbiAqIEhhbmRsZXMgdGhlIHRhc2sgdHlwZSBzd2l0Y2hpbmcgd2l0aGluIGEgZ2l2ZW4gdGFzayBjb250ZXh0LlxuICogVXBkYXRlcyB0aGUgb3B0aW9uIGNvbnRhaW5lciB3aXRoIHRoZSBzcGVjaWZpYyB0ZW1wbGF0ZSBjb250ZW50IGJhc2VkIG9uIHRoZSBzZWxlY3RlZCB0YXNrIHR5cGUuXG4gKiBJbml0aWFsaXplcyBhZGRpdGlvbmFsIHdpZGdldCBjb21wb25lbnRzIGFuZCBjaGVja2JveCBzZWxlY3Rpb24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmV4cG9ydCBjb25zdCB0YXNrVHlwZUhhbmRsZVN3aXRjaCA9IGZ1bmN0aW9uKCkge1xuXHRjb25zdCAkdGhpcyA9ICQoIHRoaXMgKTtcblx0Y29uc3QgJHRhc2sgPSAkdGhpcy5jbG9zZXN0KCBzZWxlY3RvcnMudGFzayApO1xuXG5cdCQoIHNlbGVjdG9ycy50eXBlT3B0aW9ucywgJHRhc2sgKS5odG1sKCAnJyApO1xuXG5cdC8vIFJldHJpZXZlIHRoZSB0YXNrIHR5cGUgZnJvbSB0aGUgaGlkZGVuIGlucHV0IGZpZWxkIHdpdGhpbiB0aGUgY2xpY2tlZCBjaG9pY2UuXG5cdGNvbnN0IHRhc2tUeXBlID0gJHRoaXMuaXMoICc6Y2hlY2tlZCcgKSAmJiAkdGhpcy52YWwoKTtcblxuXHQvLyBDaGVjayBpZiBhIHRhc2sgdHlwZSBpcyBkZWZpbmVkLlxuXHRpZiAoICEgdGFza1R5cGUgKSB7XG5cdFx0cmV0dXJuO1xuXHR9XG5cblx0Ly8gTG9hZCB0aGUgc3BlY2lmaWMgdGVtcGxhdGUgZnJvbSBUZW1wbGF0ZXMuXG5cdGNvbnN0IHRtcGxUYXNrVHlwZSA9IFRlbXBsYXRlcy5nZXQoIGB3cGZvcm1zLSR7IEVOVElUWV9TTFVHIH0tYnVpbGRlci1jb250ZW50LXRhc2stJHsgdGFza1R5cGUgfWAgKTtcblxuXHQvLyBJZiB0aGUgdGVtcGxhdGUgZXhpc3RzLCBpbnNlcnQgdGhlIGdlbmVyYXRlZCBIVE1MIGludG8gdGhlIG9wdGlvbnMnIGNvbnRhaW5lci5cblx0JCggc2VsZWN0b3JzLnR5cGVPcHRpb25zLCAkdGFzayApLmh0bWwoIHRtcGxUYXNrVHlwZSgge1xuXHRcdGNvbm5lY3Rpb246IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25EYXRhKCAkdGFzayApLmNvbm5lY3Rpb24sXG5cdFx0YWRkaXRpb25hbEZpZWxkczogd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzLmFkZGl0aW9uYWxGaWVsZHMsXG5cdFx0ZmlsZUZvcm1hdHM6IHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5maWxlRm9ybWF0cyxcblx0XHRleHBvcnREZXN0aW5hdGlvbnM6IHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5leHBvcnREZXN0aW5hdGlvbnMubGFiZWxzLFxuXHRcdGRlbGl2ZXJ5QWRkb25zOiB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMuZXhwb3J0RGVzdGluYXRpb25zLmVkdWNhdGlvbkFkZG9ucyxcblx0XHRhY3RpdmF0ZWRBZGRvbnM6IHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5leHBvcnREZXN0aW5hdGlvbnMuYWN0aXZhdGVkQWRkb25zLFxuXHRcdGVudHJpZXNOdW1iZXJPcHRpb25zOiB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMuZW50cmllc051bWJlck9wdGlvbnMsXG5cdFx0cHJvdmlkZXI6IEVOVElUWV9TTFVHLFxuXHR9ICkgKTtcblxuXHRjb25zdCBoYW5kbGVycyA9IHtcblx0XHRleHBvcnQ6IGV4cG9ydFR5cGVPblN3aXRjaCxcblx0XHRkZWxldGU6IGRlbGV0ZVR5cGVPblN3aXRjaCxcblx0fTtcblxuXHRoYW5kbGVyc1sgdGFza1R5cGUgXT8uKCAkdGFzayApO1xufTtcbiJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7QUFXQSxJQUFBQSxVQUFBLEdBQUFDLE9BQUE7QUFDQSxJQUFBQyxRQUFBLEdBQUFELE9BQUE7QUFDQSxJQUFBRSxTQUFBLEdBQUFGLE9BQUE7QUFDQSxJQUFBRyxTQUFBLEdBQUFILE9BQUE7QUFDQSxJQUFBSSxrQkFBQSxHQUFBSixPQUFBO0FBZkE7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBT0EsSUFBTUssQ0FBQyxHQUFHQyxNQUFNOztBQUVoQjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDTyxTQUFTQyxzQkFBc0JBLENBQUVDLEtBQUssRUFBRztFQUMvQztFQUNBLElBQU1DLGNBQWMsR0FBR0Msb0JBQVMsQ0FBQ0MsR0FBRyxZQUFBQyxNQUFBLENBQWNDLHNCQUFXLDJDQUEwQyxDQUFDO0VBQ3hHLElBQU1DLFVBQVUsR0FBR0MsR0FBRyxDQUFDQyxTQUFTLENBQUUsS0FBSyxFQUFFLElBQUksRUFBRSxJQUFLLENBQUM7RUFDckQsSUFBQUMscUJBQUEsR0FBdUJDLGtCQUFPLENBQUNDLEdBQUcsQ0FBQ0MsaUJBQWlCLENBQUVaLEtBQU0sQ0FBQztJQUFyRGEsVUFBVSxHQUFBSixxQkFBQSxDQUFWSSxVQUFVOztFQUVsQjtFQUNBaEIsQ0FBQyxDQUFFaUIsb0JBQVMsQ0FBQ0MsaUJBQWlCLEVBQUVmLEtBQU0sQ0FBQyxDQUFDZ0IsSUFBSSxDQUFFZixjQUFjLENBQUU7SUFDN0RZLFVBQVUsRUFBVkEsVUFBVTtJQUNWUCxVQUFVLEVBQVZBLFVBQVU7SUFDVlcsUUFBUSxFQUFFWjtFQUNYLENBQUUsQ0FBRSxDQUFDOztFQUVMO0VBQ0FSLENBQUMsQ0FBRSx1QkFB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
},{"./constants":4,"./export.to.js":8,"./filters":9,"./on.duplicate.option.js":12,"./schedule":13}],3:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.collapseConnections = collapseConnections;
exports.connectionsList = exports.connectionRender = exports.connectionDataLoad = exports.connectionCreate = void 0;
exports.getConnectionQueueData = getConnectionQueueData;
exports.getDefaultConnection = void 0;
var _utils = require("./utils");
var _constants = require("./constants");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); } /* global wpf, WPFormsBuilder */ /**
 * Connection-related functions for Entry Automation.
 *
 * @since 1.0.0
 */
var $ = jQuery;

/**
 * Render tasks.
 *
 * @since 1.0.0
 *
 * @param {Object} data Task data.
 */
var connectionRender = exports.connectionRender = function connectionRender(data) {
  var _data$connection, _data$connection2;
  var tmplConnection = _constants.Templates.get("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task"));
  var connectionHtml = tmplConnection({
    connection: data.connection,
    provider: _constants.ENTITY_SLUG
  });
  if ((_data$connection = data.connection) !== null && _data$connection !== void 0 && _data$connection.clonedBy) {
    var $clonedConnection = _constants.$elements.$tasks.find("".concat(_constants.selectors.taskRoot, "[data-connection_id=\"").concat(data.connection.clonedBy, "\"]"));
    $(connectionHtml).insertAfter($clonedConnection);
  } else {
    _constants.$elements.$tasks.append(connectionHtml);
  }
  if ((_data$connection2 = data.connection) !== null && _data$connection2 !== void 0 && _data$connection2.isNew) {
    return;
  }

  // Save additional fields to cache.
  if (!_constants.Cache.get(_constants.ENTITY_SLUG, "connection_".concat(data.connection.id, "_additional_field"))) {
    _constants.Cache.set(_constants.ENTITY_SLUG, "connection_".concat(data.connection.id, "_additional_field"), data.connection.additional_field);
  }

  // Save form fields to cache.
  if (!_constants.Cache.get(_constants.ENTITY_SLUG, "connection_".concat(data.connection.id, "_form_field"))) {
    _constants.Cache.set(_constants.ENTITY_SLUG, "connection_".concat(data.connection.id, "_form_field"), data.connection.form_field);
  }
  var $connection = _constants.$elements.$tasks.find("".concat(_constants.selectors.taskRoot, "[data-connection_id=\"").concat(data.connection.id, "\"]"));
  $(_constants.selectors.actionType + ':checked', $connection).trigger('change');
};

/**
 * Get the list of connections.
 *
 * @since 1.0.0
 *
 * @return {Object|false} List of connections.
 */
var connectionsList = exports.connectionsList = function connectionsList() {
  var $connections = _constants.$elements.$tasks.find(_constants.selectors.taskRoot);
  if (!$connections.length) {
    return false;
  }
  var list = {};
  $connections.each(function () {
    var connectionId = _constants.Builder.app.getConnectionId($(this));
    var connection = _constants.Cache.getById(_constants.ENTITY_SLUG, 'connections', connectionId);
    list[connectionId] = connection.name;
  });
  return list;
};

/**
 * Get the default connection object.
 *
 * @since 1.0.0
 *
 * @param {string} name Connection name.
 * @param {Object} args Optional. Additional arguments to set in the connection object.
 *
 * @return {Object} Default connection object.
 */
var getDefaultConnection = exports.getDefaultConnection = function getDefaultConnection(name) {
  var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var formFields = wpf.getFields(false, true, true);

  /* eslint-disable camelcase */
  return _objectSpread({
    id: (0, _utils.getRandomId)(),
    name: name,
    isNew: !(args !== null && args !== void 0 && args.clonedBy),
    status: '1',
    file_name: '{form_name} {date format="Timestamp"}',
    email: {
      address: '{admin_email}'
    },
    filters: {
      statuses: ['published'] // Default status.
    },
    form_field: Object.values(formFields).map(function (field) {
      return field.id;
    }),
    schedule: {
      start: 'today',
      end: '',
      frequency: 'week',
      days: _constants.frequencyChoices.week.map(function (day) {
        return day.value;
      }),
      time: '12:00 AM'
    }
  }, args);
  /* eslint-enable camelcase */
};

/**
 * Retrieves data regarding the existing connection queue, including the total
 * number of connections, details of all connections, and specific information
 * about the first connection in the queue.
 *
 * @since 1.0.0
 *
 * @param {Object} connection Connection data.
 *
 * @return {Object} An object containing the connection queue data.
 */
function getConnectionQueueData(connection) {
  var connections = connectionsList();
  if (!connections || Object.keys(connections)[0] === connection.id) {
    return {};
  }

  // Get the first connection name for parent task reference.
  var firstConnectionId = Object.keys(connections)[0] || '';

  /* eslint-disable camelcase */
  return {
    queue: Object.keys(connections).length,
    task: firstConnectionId // Set the first connection as the default parent task
  };
  /* eslint-enable camelcase */
}

/**
 * Create a connection using the user entered name.
 *
 * @since 1.0.0
 *
 * @param {string} name            Connection name.
 * @param {Object} [currentValues] Optional. Values to pre-populate in the connection.
 */
var connectionCreate = exports.connectionCreate = function connectionCreate(name) {
  var currentValues = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var connection = getDefaultConnection(name, currentValues);
  connection.schedule = _objectSpread(_objectSpread({}, connection.schedule), getConnectionQueueData(connection));
  _constants.Cache.addTo(_constants.ENTITY_SLUG, 'connections', connection.id, connection);
  connectionRender({
    connection: connection
  });
};

/**
 * Loads connection data from the server and caches it locally.
 *
 * This function sends a request to fetch connection-related data. Upon successful retrieval,
 * it caches the data for the specified entity slug and resolves the promise with the data.
 *
 * The function handles the response validation by checking for success and the presence
 * of the required `connections` property in the received data.
 *
 * @since 1.0.0
 *
 * @return {Promise} Promise that resolves with the data object if the request is successful.
 */
var connectionDataLoad = exports.connectionDataLoad = function connectionDataLoad() {
  _constants.Builder.app.getEntityError().hide();
  return new Promise(function (resolve, reject) {
    _constants.Ajax.request(_constants.ENTITY_SLUG, {
      data: {
        task: 'tasks_get'
      }
    }, {
      lock: _constants.$elements.$panel.find('.wpforms-builder-entity-connections-save-lock'),
      spinner: _constants.$elements.$panel.find('.wpforms-builder-provider-title-spinner')
    }).then(function (response) {
      if (!response.success || !_.has(response.data, 'connections')) {
        reject(response);
        return;
      }
      ['connections', 'dropbox_folders'].forEach(function (dataType) {
        _constants.Cache.set(_constants.ENTITY_SLUG, dataType, jQuery.extend({}, response.data[dataType]));
      });
      resolve(response.data);
    }).catch(function (error) {
      _constants.Builder.app.showError();
      reject(error);
    });
  });
};

/**
 * Collapses all task connection elements within the task root.
 *
 * @since 1.0.0
 */
function collapseConnections() {
  var $connections = _constants.$elements.$tasks.find(_constants.selectors.taskRoot);
  $connections.each(function () {
    var $toggle = $('.wpforms-builder-settings-block-toggle', $(this));
    WPFormsBuilder.settingsBlockPanelToggle($toggle, false);
  });
}
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfdXRpbHMiLCJyZXF1aXJlIiwiX2NvbnN0YW50cyIsIl90eXBlb2YiLCJvIiwiU3ltYm9sIiwiaXRlcmF0b3IiLCJjb25zdHJ1Y3RvciIsInByb3RvdHlwZSIsIm93bktleXMiLCJlIiwiciIsInQiLCJPYmplY3QiLCJrZXlzIiwiZ2V0T3duUHJvcGVydHlTeW1ib2xzIiwiZmlsdGVyIiwiZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yIiwiZW51bWVyYWJsZSIsInB1c2giLCJhcHBseSIsIl9vYmplY3RTcHJlYWQiLCJhcmd1bWVudHMiLCJsZW5ndGgiLCJmb3JFYWNoIiwiX2RlZmluZVByb3BlcnR5IiwiZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9ycyIsImRlZmluZVByb3BlcnRpZXMiLCJkZWZpbmVQcm9wZXJ0eSIsIl90b1Byb3BlcnR5S2V5IiwidmFsdWUiLCJjb25maWd1cmFibGUiLCJ3cml0YWJsZSIsImkiLCJfdG9QcmltaXRpdmUiLCJ0b1ByaW1pdGl2ZSIsImNhbGwiLCJUeXBlRXJyb3IiLCJTdHJpbmciLCJOdW1iZXIiLCIkIiwialF1ZXJ5IiwiY29ubmVjdGlvblJlbmRlciIsImV4cG9ydHMiLCJkYXRhIiwiX2RhdGEkY29ubmVjdGlvbiIsIl9kYXRhJGNvbm5lY3Rpb24yIiwidG1wbENvbm5lY3Rpb24iLCJUZW1wbGF0ZXMiLCJnZXQiLCJjb25jYXQiLCJFTlRJVFlfU0xVRyIsImNvbm5lY3Rpb25IdG1sIiwiY29ubmVjdGlvbiIsInByb3ZpZGVyIiwiY2xvbmVkQnkiLCIkY2xvbmVkQ29ubmVjdGlvbiIsIiRlbGVtZW50cyIsIiR0YXNrcyIsImZpbmQiLCJzZWxlY3RvcnMiLCJ0YXNrUm9vdCIsImluc2VydEFmdGVyIiwiYXBwZW5kIiwiaXNOZXciLCJDYWNoZSIsImlkIiwic2V0IiwiYWRkaXRpb25hbF9maWVsZCIsImZvcm1fZmllbGQiLCIkY29ubmVjdGlvbiIsImFjdGlvblR5cGUiLCJ0cmlnZ2VyIiwiY29ubmVjdGlvbnNMaXN0IiwiJGNvbm5lY3Rpb25zIiwibGlzdCIsImVhY2giLCJjb25uZWN0aW9uSWQiLCJCdWlsZGVyIiwiYXBwIiwiZ2V0Q29ubmVjdGlvbklkIiwiZ2V0QnlJZCIsIm5hbWUiLCJnZXREZWZhdWx0Q29ubmVjdGlvbiIsImFyZ3MiLCJ1bmRlZmluZWQiLCJmb3JtRmllbGRzIiwid3BmIiwiZ2V0RmllbGRzIiwiZ2V0UmFuZG9tSWQiLCJzdGF0dXMiLCJmaWxlX25hbWUiLCJlbWFpbCIsImFkZHJlc3MiLCJmaWx0ZXJzIiwic3RhdHVzZXMiLCJ2YWx1ZXMiLCJtYXAiLCJmaWVsZCIsInNjaGVkdWxlIiwic3RhcnQiLCJlbmQiLCJmcmVxdWVuY3kiLCJkYXlzIiwiZnJlcXVlbmN5Q2hvaWNlcyIsIndlZWsiLCJkYXkiLCJ0aW1lIiwiZ2V0Q29ubmVjdGlvblF1ZXVlRGF0YSIsImNvbm5lY3Rpb25zIiwiZmlyc3RDb25uZWN0aW9uSWQiLCJxdWV1ZSIsInRhc2siLCJjb25uZWN0aW9uQ3JlYXRlIiwiY3VycmVudFZhbHVlcyIsImFkZFRvIiwiY29ubmVjdGlvbkRhdGFMb2FkIiwiZ2V0RW50aXR5RXJyb3IiLCJoaWRlIiwiUHJvbWlzZSIsInJlc29sdmUiLCJyZWplY3QiLCJBamF4IiwicmVxdWVzdCIsImxvY2siLCIkcGFuZWwiLCJzcGlubmVyIiwidGhlbiIsInJlc3BvbnNlIiwic3VjY2VzcyIsIl8iLCJoYXMiLCJkYXRhVHlwZSIsImV4dGVuZCIsImNhdGNoIiwiZXJyb3IiLCJzaG93RXJyb3IiLCJjb2xsYXBzZUNvbm5lY3Rpb25zIiwiJHRvZ2dsZSIsIldQRm9ybXNCdWlsZGVyIiwic2V0dGluZ3NCbG9ja1BhbmVsVG9nZ2xlIl0sInNvdXJjZXMiOlsiY29ubmVjdGlvbi5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKiBnbG9iYWwgd3BmLCBXUEZvcm1zQnVpbGRlciAqL1xuXG4vKipcbiAqIENvbm5lY3Rpb24tcmVsYXRlZCBmdW5jdGlvbnMgZm9yIEVudHJ5IEF1dG9tYXRpb24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmltcG9ydCB7IGdldFJhbmRvbUlkIH0gZnJvbSAnLi91dGlscyc7XG5pbXBvcnQge1xuXHRmcmVxdWVuY3lDaG9pY2VzLFxuXHQkZWxlbWVudHMsXG5cdENhY2hlLFxuXHRUZW1wbGF0ZXMsXG5cdEFqYXgsXG5cdEVOVElUWV9TTFVHLFxuXHRzZWxlY3RvcnMsXG5cdEJ1aWxkZXIsXG59IGZyb20gJy4vY29uc3RhbnRzJztcblxuY29uc3QgJCA9IGpRdWVyeTtcblxuLyoqXG4gKiBSZW5kZXIgdGFza3MuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IGRhdGEgVGFzayBkYXRhLlxuICovXG5leHBvcnQgY29uc3QgY29ubmVjdGlvblJlbmRlciA9IGZ1bmN0aW9uKCBkYXRhICkge1xuXHRjb25zdCB0bXBsQ29ubmVjdGlvbiA9IFRlbXBsYXRlcy5nZXQoIGB3cGZvcm1zLSR7IEVOVElUWV9TTFVHIH0tYnVpbGRlci1jb250ZW50LXRhc2tgICk7XG5cdGNvbnN0IGNvbm5lY3Rpb25IdG1sID0gdG1wbENvbm5lY3Rpb24oIHtcblx0XHRjb25uZWN0aW9uOiBkYXRhLmNvbm5lY3Rpb24sXG5cdFx0cHJvdmlkZXI6IEVOVElUWV9TTFVHLFxuXHR9ICk7XG5cblx0aWYgKCBkYXRhLmNvbm5lY3Rpb24/LmNsb25lZEJ5ICkge1xuXHRcdGNvbnN0ICRjbG9uZWRDb25uZWN0aW9uID0gJGVsZW1lbnRzLiR0YXNrcy5maW5kKCBgJHsgc2VsZWN0b3JzLnRhc2tSb290IH1bZGF0YS1jb25uZWN0aW9uX2lkPVwiJHsgZGF0YS5jb25uZWN0aW9uLmNsb25lZEJ5IH1cIl1gICk7XG5cblx0XHQkKCBjb25uZWN0aW9uSHRtbCApLmluc2VydEFmdGVyKCAkY2xvbmVkQ29ubmVjdGlvbiApO1xuXHR9IGVsc2Uge1xuXHRcdCRlbGVtZW50cy4kdGFza3MuYXBwZW5kKCBjb25uZWN0aW9uSHRtbCApO1xuXHR9XG5cblx0aWYgKCBkYXRhLmNvbm5lY3Rpb24/LmlzTmV3ICkge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdC8vIFNhdmUgYWRkaXRpb25hbCBmaWVsZHMgdG8gY2FjaGUuXG5cdGlmICggISBDYWNoZS5nZXQoIEVOVElUWV9TTFVHLCBgY29ubmVjdGlvbl8keyBkYXRhLmNvbm5lY3Rpb24uaWQgfV9hZGRpdGlvbmFsX2ZpZWxkYCApICkge1xuXHRcdENhY2hlLnNldCggRU5USVRZX1NMVUcsIGBjb25uZWN0aW9uXyR7IGRhdGEuY29ubmVjdGlvbi5pZCB9X2FkZGl0aW9uYWxfZmllbGRgLCBkYXRhLmNvbm5lY3Rpb24uYWRkaXRpb25hbF9maWVsZCApO1xuXHR9XG5cblx0Ly8gU2F2ZSBmb3JtIGZpZWxkcyB0byBjYWNoZS5cblx0aWYgKCAhIENhY2hlLmdldCggRU5USVRZX1NMVUcsIGBjb25uZWN0aW9uXyR7IGRhdGEuY29ubmVjdGlvbi5pZCB9X2Zvcm1fZmllbGRgICkgKSB7XG5cdFx0Q2FjaGUuc2V0KCBFTlRJVFlfU0xVRywgYGNvbm5lY3Rpb25fJHsgZGF0YS5jb25uZWN0aW9uLmlkIH1fZm9ybV9maWVsZGAsIGRhdGEuY29ubmVjdGlvbi5mb3JtX2ZpZWxkICk7XG5cdH1cblxuXHRjb25zdCAkY29ubmVjdGlvbiA9ICRlbGVtZW50cy4kdGFza3MuZmluZCggYCR7IHNlbGVjdG9ycy50YXNrUm9vdCB9W2RhdGEtY29ubmVjdGlvbl9pZD1cIiR7IGRhdGEuY29ubmVjdGlvbi5pZCB9XCJdYCApO1xuXG5cdCQoIHNlbGVjdG9ycy5hY3Rpb25UeXBlICsgJzpjaGVja2VkJywgJGNvbm5lY3Rpb24gKS50cmlnZ2VyKCAnY2hhbmdlJyApO1xufTtcblxuLyoqXG4gKiBHZXQgdGhlIGxpc3Qgb2YgY29ubmVjdGlvbnMuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHJldHVybiB7T2JqZWN0fGZhbHNlfSBMaXN0IG9mIGNvbm5lY3Rpb25zLlxuICovXG5leHBvcnQgY29uc3QgY29ubmVjdGlvbnNMaXN0ID0gZnVuY3Rpb24oKSB7XG5cdGNvbnN0ICRjb25uZWN0aW9ucyA9ICRlbGVtZW50cy4kdGFza3MuZmluZCggc2VsZWN0b3JzLnRhc2tSb290ICk7XG5cblx0aWYgKCAhICRjb25uZWN0aW9ucy5sZW5ndGggKSB7XG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9XG5cblx0Y29uc3QgbGlzdCA9IHt9O1xuXG5cdCRjb25uZWN0aW9ucy5lYWNoKCBmdW5jdGlvbigpIHtcblx0XHRjb25zdCBjb25uZWN0aW9uSWQgPSBCdWlsZGVyLmFwcC5nZXRDb25uZWN0aW9uSWQoICQoIHRoaXMgKSApO1xuXHRcdGNvbnN0IGNvbm5lY3Rpb24gPSBDYWNoZS5nZXRCeUlkKCBFTlRJVFlfU0xVRywgJ2Nvbm5lY3Rpb25zJywgY29ubmVjdGlvbklkICk7XG5cdFx0bGlzdFsgY29ubmVjdGlvbklkIF0gPSBjb25uZWN0aW9uLm5hbWU7XG5cdH0gKTtcblxuXHRyZXR1cm4gbGlzdDtcbn07XG5cbi8qKlxuICogR2V0IHRoZSBkZWZhdWx0IGNvbm5lY3Rpb24gb2JqZWN0LlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lIENvbm5lY3Rpb24gbmFtZS5cbiAqIEBwYXJhbSB7T2JqZWN0fSBhcmdzIE9wdGlvbmFsLiBBZGRpdGlvbmFsIGFyZ3VtZW50cyB0byBzZXQgaW4gdGhlIGNvbm5lY3Rpb24gb2JqZWN0LlxuICpcbiAqIEByZXR1cm4ge09iamVjdH0gRGVmYXVsdCBjb25uZWN0aW9uIG9iamVjdC5cbiAqL1xuZXhwb3J0IGNvbnN0IGdldERlZmF1bHRDb25uZWN0aW9uID0gZnVuY3Rpb24oIG5hbWUsIGFyZ3MgPSB7fSApIHtcblx0Y29uc3QgZm9ybUZpZWxkcyA9IHdwZi5nZXRGaWVsZHMoIGZhbHNlLCB0cnVlLCB0cnVlICk7XG5cblx0LyogZXNsaW50LWRpc2FibGUgY2FtZWxjYXNlICovXG5cdHJldHVybiB7XG5cdFx0aWQ6IGdldFJhbmRvbUlkKCksXG5cdFx0bmFtZSxcblx0XHRpc05ldzogISBhcmdzPy5jbG9uZWRCeSxcblx0XHRzdGF0dXM6ICcxJyxcblx0XHRmaWxlX25hbWU6ICd7Zm9ybV9uYW1lfSB7ZGF0ZSBmb3JtYXQ9XCJUaW1lc3RhbXBcIn0nLFxuXHRcdGVtYWlsOiB7XG5cdFx0XHRhZGRyZXNzOiAne2FkbWluX2VtYWlsfScsXG5cdFx0fSxcblx0XHRmaWx0ZXJzOiB7XG5cdFx0XHRzdGF0dXNlczogWyAncHVibGlzaGVkJyBdLCAvLyBEZWZhdWx0IHN0YXR1cy5cblx0XHR9LFxuXHRcdGZvcm1fZmllbGQ6IE9iamVjdC52YWx1ZXMoIGZvcm1GaWVsZHMgKS5tYXAoICggZmllbGQgKSA9PiBmaWVsZC5pZCApLFxuXHRcdHNjaGVkdWxlOiB7XG5cdFx0XHRzdGFydDogJ3RvZGF5Jyxcblx0XHRcdGVuZDogJycsXG5cdFx0XHRmcmVxdWVuY3k6ICd3ZWVrJyxcblx0XHRcdGRheXM6IGZyZXF1ZW5jeUNob2ljZXMud2Vlay5tYXAoICggZGF5ICkgPT4gZGF5LnZhbHVlICksXG5cdFx0XHR0aW1lOiAnMTI6MDAgQU0nLFxuXHRcdH0sXG5cdFx0Li4uYXJncyxcblx0fTtcblx0LyogZXNsaW50LWVuYWJsZSBjYW1lbGNhc2UgKi9cbn07XG5cbi8qKlxuICogUmV0cmlldmVzIGRhdGEgcmVnYXJkaW5nIHRoZSBleGlzdGluZyBjb25uZWN0aW9uIHF1ZXVlLCBpbmNsdWRpbmcgdGhlIHRvdGFsXG4gKiBudW1iZXIgb2YgY29ubmVjdGlvbnMsIGRldGFpbHMgb2YgYWxsIGNvbm5lY3Rpb25zLCBhbmQgc3BlY2lmaWMgaW5mb3JtYXRpb25cbiAqIGFib3V0IHRoZSBmaXJzdCBjb25uZWN0aW9uIGluIHRoZSBxdWV1ZS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gY29ubmVjdGlvbiBDb25uZWN0aW9uIGRhdGEuXG4gKlxuICogQHJldHVybiB7T2JqZWN0fSBBbiBvYmplY3QgY29udGFpbmluZyB0aGUgY29ubmVjdGlvbiBxdWV1ZSBkYXRhLlxuICovXG5leHBvcnQgZnVuY3Rpb24gZ2V0Q29ubmVjdGlvblF1ZXVlRGF0YSggY29ubmVjdGlvbiApIHtcblx0Y29uc3QgY29ubmVjdGlvbnMgPSBjb25uZWN0aW9uc0xpc3QoKTtcblxuXHRpZiAoICEgY29ubmVjdGlvbnMgfHwgT2JqZWN0LmtleXMoIGNvbm5lY3Rpb25zIClbIDAgXSA9PT0gY29ubmVjdGlvbi5pZCApIHtcblx0XHRyZXR1cm4ge307XG5cdH1cblxuXHQvLyBHZXQgdGhlIGZpcnN0IGNvbm5lY3Rpb24gbmFtZSBmb3IgcGFyZW50IHRhc2sgcmVmZXJlbmNlLlxuXHRjb25zdCBmaXJzdENvbm5lY3Rpb25JZCA9IE9iamVjdC5rZXlzKCBjb25uZWN0aW9ucyApWyAwIF0gfHwgJyc7XG5cblx0LyogZXNsaW50LWRpc2FibGUgY2FtZWxjYXNlICovXG5cdHJldHVybiB7XG5cdFx0cXVldWU6IE9iamVjdC5rZXlzKCBjb25uZWN0aW9ucyApLmxlbmd0aCxcblx0XHR0YXNrOiBmaXJzdENvbm5lY3Rpb25JZCwgLy8gU2V0IHRoZSBmaXJzdCBjb25uZWN0aW9uIGFzIHRoZSBkZWZhdWx0IHBhcmVudCB0YXNrXG5cdH07XG5cdC8qIGVzbGludC1lbmFibGUgY2FtZWxjYXNlICovXG59XG5cbi8qKlxuICogQ3JlYXRlIGEgY29ubmVjdGlvbiB1c2luZyB0aGUgdXNlciBlbnRlcmVkIG5hbWUuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtzdHJpbmd9IG5hbWUgICAgICAgICAgICBDb25uZWN0aW9uIG5hbWUuXG4gKiBAcGFyYW0ge09iamVjdH0gW2N1cnJlbnRWYWx1ZXNdIE9wdGlvbmFsLiBWYWx1ZXMgdG8gcHJlLXBvcHVsYXRlIGluIHRoZSBjb25uZWN0aW9uLlxuICovXG5leHBvcnQgY29uc3QgY29ubmVjdGlvbkNyZWF0ZSA9IGZ1bmN0aW9uKCBuYW1lLCBjdXJyZW50VmFsdWVzID0ge30gKSB7XG5cdGNvbnN0IGNvbm5lY3Rpb24gPSBnZXREZWZhdWx0Q29ubmVjdGlvbiggbmFtZSwgY3VycmVudFZhbHVlcyApO1xuXG5cdGNvbm5lY3Rpb24uc2NoZWR1bGUgPSB7XG5cdFx0Li4uY29ubmVjdGlvbi5zY2hlZHVsZSxcblx0XHQuLi5nZXRDb25uZWN0aW9uUXVldWVEYXRhKCBjb25uZWN0aW9uICksXG5cdH07XG5cblx0Q2FjaGUuYWRkVG8oIEVOVElUWV9TTFVHLCAnY29ubmVjdGlvbnMnLCBjb25uZWN0aW9uLmlkLCBjb25uZWN0aW9uICk7XG5cblx0Y29ubmVjdGlvblJlbmRlcigge1xuXHRcdGNvbm5lY3Rpb24sXG5cdH0gKTtcbn07XG5cbi8qKlxuICogTG9hZHMgY29ubmVjdGlvbiBkYXRhIGZyb20gdGhlIHNlcnZlciBhbmQgY2FjaGVzIGl0IGxvY2FsbHkuXG4gKlxuICogVGhpcyBmdW5jdGlvbiBzZW5kcyBhIHJlcXVlc3QgdG8gZmV0Y2ggY29ubmVjdGlvbi1yZWxhdGVkIGRhdGEuIFVwb24gc3VjY2Vzc2Z1bCByZXRyaWV2YWwsXG4gKiBpdCBjYWNoZXMgdGhlIGRhdGEgZm9yIHRoZSBzcGVjaWZpZWQgZW50aXR5IHNsdWcgYW5kIHJlc29sdmVzIHRoZSBwcm9taXNlIHdpdGggdGhlIGRhdGEuXG4gKlxuICogVGhlIGZ1bmN0aW9uIGhhbmRsZXMgdGhlIHJlc3BvbnNlIHZhbGlkYXRpb24gYnkgY2hlY2tpbmcgZm9yIHN1Y2Nlc3MgYW5kIHRoZSBwcmVzZW5jZVxuICogb2YgdGhlIHJlcXVpcmVkIGBjb25uZWN0aW9uc2AgcHJvcGVydHkgaW4gdGhlIHJlY2VpdmVkIGRhdGEuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHJldHVybiB7UHJvbWlzZX0gUHJvbWlzZSB0aGF0IHJlc29sdmVzIHdpdGggdGhlIGRhdGEgb2JqZWN0IGlmIHRoZSByZXF1ZXN0IGlzIHN1Y2Nlc3NmdWwuXG4gKi9cbmV4cG9ydCBjb25zdCBjb25uZWN0aW9uRGF0YUxvYWQgPSBmdW5jdGlvbigpIHtcblx0QnVpbGRlci5hcHAuZ2V0RW50aXR5RXJyb3IoKS5oaWRlKCk7XG5cblx0cmV0dXJuIG5ldyBQcm9taXNlKCAoIHJlc29sdmUsIHJlamVjdCApID0+IHtcblx0XHRBamF4LnJlcXVlc3QoXG5cdFx0XHRFTlRJVFlfU0xVRyxcblx0XHRcdHtcblx0XHRcdFx0ZGF0YToge1xuXHRcdFx0XHRcdHRhc2s6ICd0YXNrc19nZXQnLFxuXHRcdFx0XHR9LFxuXHRcdFx0fSxcblx0XHRcdHtcblx0XHRcdFx0bG9jazogJGVsZW1lbnRzLiRwYW5lbC5maW5kKCAnLndwZm9ybXMtYnVpbGRlci1lbnRpdHktY29ubmVjdGlvbnMtc2F2ZS1sb2NrJyApLFxuXHRcdFx0XHRzcGlubmVyOiAkZWxlbWVudHMuJHBhbmVsLmZpbmQoICcud3Bmb3Jtcy1idWlsZGVyLXByb3ZpZGVyLXRpdGxlLXNwaW5uZXInICksXG5cdFx0XHR9XG5cdFx0KS50aGVuKCAoIHJlc3BvbnNlICkgPT4ge1xuXHRcdFx0aWYgKFxuXHRcdFx0XHQhIHJlc3BvbnNlLnN1Y2Nlc3MgfHxcblx0XHRcdFx0ISBfLmhhcyggcmVzcG9uc2UuZGF0YSwgJ2Nvbm5lY3Rpb25zJyApXG5cdFx0XHQpIHtcblx0XHRcdFx0cmVqZWN0KCByZXNwb25zZSApO1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRbXG5cdFx0XHRcdCdjb25uZWN0aW9ucycsXG5cdFx0XHRcdCdkcm9wYm94X2ZvbGRlcnMnLFxuXHRcdFx0XS5mb3JFYWNoKCAoIGRhdGFUeXBlICkgPT4ge1xuXHRcdFx0XHRDYWNoZS5zZXQoIEVOVElUWV9TTFVHLCBkYXRhVHlwZSwgalF1ZXJ5LmV4dGVuZCgge30sIHJlc3BvbnNlLmRhdGFbIGRhdGFUeXBlIF0gKSApO1xuXHRcdFx0fSApO1xuXG5cdFx0XHRyZXNvbHZlKCByZXNwb25zZS5kYXRhICk7XG5cdFx0fSApLmNhdGNoKCAoIGVycm9yICkgPT4ge1xuXHRcdFx0QnVpbGRlci5hcHAuc2hvd0Vycm9yKCk7XG5cdFx0XHRyZWplY3QoIGVycm9yICk7XG5cdFx0fSApO1xuXHR9ICk7XG59O1xuXG4vKipcbiAqIENvbGxhcHNlcyBhbGwgdGFzayBjb25uZWN0aW9uIGVsZW1lbnRzIHdpdGhpbiB0aGUgdGFzayByb290LlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICovXG5leHBvcnQgZnVuY3Rpb24gY29sbGFwc2VDb25uZWN0aW9ucygpIHtcblx0Y29uc3QgJGNvbm5lY3Rpb25zID0gJGVsZW1lbnRzLiR0YXNrcy5maW5kKCBzZWxlY3RvcnMudGFza1Jvb3QgKTtcblxuXHQkY29ubmVjdGlvbnMuZWFjaCggZnVuY3Rpb24oKSB7XG5cdFx0Y29uc3QgJHRvZ2dsZSA9ICQoICcud3Bmb3Jtcy1idWlsZGVyLXNldHRpbmdzLWJsb2NrLXRvZ2dsZScsICQoIHRoaXMgKSApO1xuXG5cdFx0V1BGb3Jtc0J1aWxkZXIuc2V0dGluZ3NCbG9ja1BhbmVsVG9nZ2xlKCAkdG9nZ2xlLCBmYWxzZSApO1xuXHR9ICk7XG59XG4iXSwibWFwcGluZ3MiOiI7Ozs7Ozs7OztBQU9BLElBQUFBLE1BQUEsR0FBQUMsT0FBQTtBQUNBLElBQUFDLFVBQUEsR0FBQUQsT0FBQTtBQVNxQixTQUFBRSxRQUFBQyxDQUFBLHNDQUFBRCxPQUFBLHdCQUFBRSxNQUFBLHVCQUFBQSxNQUFBLENBQUFDLFFBQUEsYUFBQUYsQ0FBQSxrQkFBQUEsQ0FBQSxnQkFBQUEsQ0FBQSxXQUFBQSxDQUFBLHlCQUFBQyxNQUFBLElBQUFELENBQUEsQ0FBQUcsV0FBQSxLQUFBRixNQUFBLElBQUFELENBQUEsS0FBQUMsTUFBQSxDQUFBRyxTQUFBLHFCQUFBSixDQUFBLEtBQUFELE9BQUEsQ0FBQUMsQ0FBQTtBQUFBLFNBQUFLLFFBQUFDLENBQUEsRUFBQUMsQ0FBQSxRQUFBQyxDQUFBLEdBQUFDLE1BQUEsQ0FBQUMsSUFBQSxDQUFBSixDQUFBLE9BQUFHLE1BQUEsQ0FBQUUscUJBQUEsUUFBQVgsQ0FBQSxHQUFBUyxNQUFBLENBQUFFLHFCQUFBLENBQUFMLENBQUEsR0FBQUMsQ0FBQSxLQUFBUCxDQUFBLEdBQUFBLENBQUEsQ0FBQVksTUFBQSxXQUFBTCxDQUFBLFdBQUFFLE1BQUEsQ0FBQUks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
},{"./constants":4,"./utils":14}],4:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.selectors = exports.operatorsObject = exports.frequencyChoices = exports.Templates = exports.ENTITY_SLUG = exports.Cache = exports.Builder = exports.Ajax = exports.$elements = void 0;
var _utils = require("./utils.js");
/* global wpforms_builder, wpformsEntryAutomationBuilderVars, WPForms */
/**
 * Constants and shared variables for Entry Automation.
 *
 * @since 1.0.0
 */

/**
 * The slug used to identify the Entry Automation entity.
 *
 * @since 1.0.0
 */
var ENTITY_SLUG = exports.ENTITY_SLUG = 'entry_automation';

/**
 * An object containing operators for comparison operations.
 *
 * @since 1.0.0
 */
var operatorsObject = exports.operatorsObject = {
  contains: wpforms_builder.operator_contains,
  // eslint-disable-next-line camelcase
  contains_not: wpforms_builder.operator_not_contains,
  is: wpforms_builder.operator_is,
  // eslint-disable-next-line camelcase
  is_not: wpforms_builder.operator_is_not
};

/**
 * An object representing the available frequency choices for scheduling tasks or entries.
 * Contains options for weekly and monthly frequencies.
 *
 * @since 1.0.0
 *
 * @property {Array<string>} week  An array of weekday names retrieved from l10n.weekDays.
 * @property {Function}      month A function that generates an array of days for a month.
 */
var frequencyChoices = exports.frequencyChoices = {
  week: wpformsEntryAutomationBuilderVars.weekDays,
  month: (0, _utils.generateDaysArray)()
};

/**
 * jQuery elements.
 *
 * @since 1.0.0
 *
 * @type {Object}
 */
var $elements = exports.$elements = {
  $builder: jQuery('#wpforms-builder'),
  $panel: jQuery("#".concat(ENTITY_SLUG)),
  $tasks: jQuery("#".concat(ENTITY_SLUG, " .wpforms-builder-entity-connections"))
};

/**
 * Contains selectors used in the application for querying DOM elements.
 * Defines a CSS selector for the task options element in the WPForms builder settings block.
 *
 * @since 1.0.0
 */
var selectors = exports.selectors = {
  task: '.wpforms-builder-settings-block-content',
  taskRoot: '.wpforms-builder-entity-connection',
  actionType: '.wpforms-larger-icon-choice input',
  typeOptions: '.wpforms-entry-automation-task-type-options',
  fileFormat: '.wpforms-entry-automation-file-format',
  exportToOptions: '.wpforms-entry-automation-task-export-to-options',
  exportTo: '.wpforms-entry-automation-export-to',
  onDuplicate: '.wpforms-entry-automation-on-duplicate',
  onDuplicateWrap: '.wpforms-entry-automation-on-duplicate-wrap',
  filterOptions: '.wpforms-entry-automation-filter-options',
  formFieldsWrapper: '.wpforms-entry-automation-form-fields-list-wrap',
  scheduleOptions: '.wpforms-entry-automation-schedule-options',
  frequency: '.wpforms-entry-automation-schedule-frequency',
  daysWrapper: '.wpforms-entry-automation-schedule-days',
  choiceJS: '.choicesjs-select',
  datePicker: '.wpforms-entry-automation-schedule-item-wrap input',
  addConnection: '.wpforms-builder-inner-provider-connection-add',
  addAccount: '.wpforms-builder-inner-provider-account-add',
  queueNoticeDisplay: '.wpforms-entry-automation-schedule-queue-notice-display',
  queueToggle: '.wpforms-entry-automation-schedule-queue-toggle',
  oneEntryPerPageWrap: '.wpforms-entry-automation-one-entry-per-page-wrap'
};

// Initialize dependencies when DOM is ready.
var Templates;
var Ajax;
var Cache;
var Builder = exports.Builder = {};
jQuery(function () {
  exports.Templates = Templates = WPForms.Admin.Builder.Templates;
  exports.Ajax = Ajax = WPForms.Admin.Builder.Ajax;
  exports.Cache = Cache = WPForms.Admin.Builder.Providers.cache;
});
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfdXRpbHMiLCJyZXF1aXJlIiwiRU5USVRZX1NMVUciLCJleHBvcnRzIiwib3BlcmF0b3JzT2JqZWN0IiwiY29udGFpbnMiLCJ3cGZvcm1zX2J1aWxkZXIiLCJvcGVyYXRvcl9jb250YWlucyIsImNvbnRhaW5zX25vdCIsIm9wZXJhdG9yX25vdF9jb250YWlucyIsImlzIiwib3BlcmF0b3JfaXMiLCJpc19ub3QiLCJvcGVyYXRvcl9pc19ub3QiLCJmcmVxdWVuY3lDaG9pY2VzIiwid2VlayIsIndwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycyIsIndlZWtEYXlzIiwibW9udGgiLCJnZW5lcmF0ZURheXNBcnJheSIsIiRlbGVtZW50cyIsIiRidWlsZGVyIiwialF1ZXJ5IiwiJHBhbmVsIiwiY29uY2F0IiwiJHRhc2tzIiwic2VsZWN0b3JzIiwidGFzayIsInRhc2tSb290IiwiYWN0aW9uVHlwZSIsInR5cGVPcHRpb25zIiwiZmlsZUZvcm1hdCIsImV4cG9ydFRvT3B0aW9ucyIsImV4cG9ydFRvIiwib25EdXBsaWNhdGUiLCJvbkR1cGxpY2F0ZVdyYXAiLCJmaWx0ZXJPcHRpb25zIiwiZm9ybUZpZWxkc1dyYXBwZXIiLCJzY2hlZHVsZU9wdGlvbnMiLCJmcmVxdWVuY3kiLCJkYXlzV3JhcHBlciIsImNob2ljZUpTIiwiZGF0ZVBpY2tlciIsImFkZENvbm5lY3Rpb24iLCJhZGRBY2NvdW50IiwicXVldWVOb3RpY2VEaXNwbGF5IiwicXVldWVUb2dnbGUiLCJvbmVFbnRyeVBlclBhZ2VXcmFwIiwiVGVtcGxhdGVzIiwiQWpheCIsIkNhY2hlIiwiQnVpbGRlciIsIldQRm9ybXMiLCJBZG1pbiIsIlByb3ZpZGVycyIsImNhY2hlIl0sInNvdXJjZXMiOlsiY29uc3RhbnRzLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCB3cGZvcm1zX2J1aWxkZXIsIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycywgV1BGb3JtcyAqL1xuLyoqXG4gKiBDb25zdGFudHMgYW5kIHNoYXJlZCB2YXJpYWJsZXMgZm9yIEVudHJ5IEF1dG9tYXRpb24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cblxuaW1wb3J0IHsgZ2VuZXJhdGVEYXlzQXJyYXkgfSBmcm9tICcuL3V0aWxzLmpzJztcblxuLyoqXG4gKiBUaGUgc2x1ZyB1c2VkIHRvIGlkZW50aWZ5IHRoZSBFbnRyeSBBdXRvbWF0aW9uIGVudGl0eS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuZXhwb3J0IGNvbnN0IEVOVElUWV9TTFVHID0gJ2VudHJ5X2F1dG9tYXRpb24nO1xuXG4vKipcbiAqIEFuIG9iamVjdCBjb250YWluaW5nIG9wZXJhdG9ycyBmb3IgY29tcGFyaXNvbiBvcGVyYXRpb25zLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICovXG5leHBvcnQgY29uc3Qgb3BlcmF0b3JzT2JqZWN0ID0ge1xuXHRjb250YWluczogd3Bmb3Jtc19idWlsZGVyLm9wZXJhdG9yX2NvbnRhaW5zLFxuXHQvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgY2FtZWxjYXNlXG5cdGNvbnRhaW5zX25vdDogd3Bmb3Jtc19idWlsZGVyLm9wZXJhdG9yX25vdF9jb250YWlucyxcblx0aXM6IHdwZm9ybXNfYnVpbGRlci5vcGVyYXRvcl9pcyxcblx0Ly8gZXNsaW50LWRpc2FibGUtbmV4dC1saW5lIGNhbWVsY2FzZVxuXHRpc19ub3Q6IHdwZm9ybXNfYnVpbGRlci5vcGVyYXRvcl9pc19ub3QsXG59O1xuXG4vKipcbiAqIEFuIG9iamVjdCByZXByZXNlbnRpbmcgdGhlIGF2YWlsYWJsZSBmcmVxdWVuY3kgY2hvaWNlcyBmb3Igc2NoZWR1bGluZyB0YXNrcyBvciBlbnRyaWVzLlxuICogQ29udGFpbnMgb3B0aW9ucyBmb3Igd2Vla2x5IGFuZCBtb250aGx5IGZyZXF1ZW5jaWVzLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwcm9wZXJ0eSB7QXJyYXk8c3RyaW5nPn0gd2VlayAgQW4gYXJyYXkgb2Ygd2Vla2RheSBuYW1lcyByZXRyaWV2ZWQgZnJvbSBsMTBuLndlZWtEYXlzLlxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gICAgICBtb250aCBBIGZ1bmN0aW9uIHRoYXQgZ2VuZXJhdGVzIGFuIGFycmF5IG9mIGRheXMgZm9yIGEgbW9udGguXG4gKi9cbmV4cG9ydCBjb25zdCBmcmVxdWVuY3lDaG9pY2VzID0ge1xuXHR3ZWVrOiB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMud2Vla0RheXMsXG5cdG1vbnRoOiBnZW5lcmF0ZURheXNBcnJheSgpLFxufTtcblxuLyoqXG4gKiBqUXVlcnkgZWxlbWVudHMuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHR5cGUge09iamVjdH1cbiAqL1xuZXhwb3J0IGNvbnN0ICRlbGVtZW50cyA9IHtcblx0JGJ1aWxkZXI6IGpRdWVyeSggJyN3cGZvcm1zLWJ1aWxkZXInICksXG5cdCRwYW5lbDogalF1ZXJ5KCBgIyR7IEVOVElUWV9TTFVHIH1gICksXG5cdCR0YXNrczogalF1ZXJ5KCBgIyR7IEVOVElUWV9TTFVHIH0gLndwZm9ybXMtYnVpbGRlci1lbnRpdHktY29ubmVjdGlvbnNgICksXG59O1xuXG4vKipcbiAqIENvbnRhaW5zIHNlbGVjdG9ycyB1c2VkIGluIHRoZSBhcHBsaWNhdGlvbiBmb3IgcXVlcnlpbmcgRE9NIGVsZW1lbnRzLlxuICogRGVmaW5lcyBhIENTUyBzZWxlY3RvciBmb3IgdGhlIHRhc2sgb3B0aW9ucyBlbGVtZW50IGluIHRoZSBXUEZvcm1zIGJ1aWxkZXIgc2V0dGluZ3MgYmxvY2suXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmV4cG9ydCBjb25zdCBzZWxlY3RvcnMgPSB7XG5cdHRhc2s6ICcud3Bmb3Jtcy1idWlsZGVyLXNldHRpbmdzLWJsb2NrLWNvbnRlbnQnLFxuXHR0YXNrUm9vdDogJy53cGZvcm1zLWJ1aWxkZXItZW50aXR5LWNvbm5lY3Rpb24nLFxuXHRhY3Rpb25UeXBlOiAnLndwZm9ybXMtbGFyZ2VyLWljb24tY2hvaWNlIGlucHV0Jyxcblx0dHlwZU9wdGlvbnM6ICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXRhc2stdHlwZS1vcHRpb25zJyxcblx0ZmlsZUZvcm1hdDogJy53cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tZmlsZS1mb3JtYXQnLFxuXHRleHBvcnRUb09wdGlvbnM6ICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXRhc2stZXhwb3J0LXRvLW9wdGlvbnMnLFxuXHRleHBvcnRUbzogJy53cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tZXhwb3J0LXRvJyxcblx0b25EdXBsaWNhdGU6ICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLW9uLWR1cGxpY2F0ZScsXG5cdG9uRHVwbGljYXRlV3JhcDogJy53cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tb24tZHVwbGljYXRlLXdyYXAnLFxuXHRmaWx0ZXJPcHRpb25zOiAnLndwZm9ybXMtZW50cnktYXV0b21hdGlvbi1maWx0ZXItb3B0aW9ucycsXG5cdGZvcm1GaWVsZHNXcmFwcGVyOiAnLndwZm9ybXMtZW50cnktYXV0b21hdGlvbi1mb3JtLWZpZWxkcy1saXN0LXdyYXAnLFxuXHRzY2hlZHVsZU9wdGlvbnM6ICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXNjaGVkdWxlLW9wdGlvbnMnLFxuXHRmcmVxdWVuY3k6ICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXNjaGVkdWxlLWZyZXF1ZW5jeScsXG5cdGRheXNXcmFwcGVyOiAnLndwZm9ybXMtZW50cnktYXV0b21hdGlvbi1zY2hlZHVsZS1kYXlzJyxcblx0Y2hvaWNlSlM6ICcuY2hvaWNlc2pzLXNlbGVjdCcsXG5cdGRhdGVQaWNrZXI6ICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXNjaGVkdWxlLWl0ZW0td3JhcCBpbnB1dCcsXG5cdGFkZENvbm5lY3Rpb246ICcud3Bmb3Jtcy1idWlsZGVyLWlubmVyLXByb3ZpZGVyLWNvbm5lY3Rpb24tYWRkJyxcblx0YWRkQWNjb3VudDogJy53cGZvcm1zLWJ1aWxkZXItaW5uZXItcHJvdmlkZXItYWNjb3VudC1hZGQnLFxuXHRxdWV1ZU5vdGljZURpc3BsYXk6ICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXNjaGVkdWxlLXF1ZXVlLW5vdGljZS1kaXNwbGF5Jyxcblx0cXVldWVUb2dnbGU6ICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXNjaGVkdWxlLXF1ZXVlLXRvZ2dsZScsXG5cdG9uZUVudHJ5UGVyUGFnZVdyYXA6ICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLW9uZS1lbnRyeS1wZXItcGFnZS13cmFwJyxcbn07XG5cbi8vIEluaXRpYWxpemUgZGVwZW5kZW5jaWVzIHdoZW4gRE9NIGlzIHJlYWR5LlxuZXhwb3J0IGxldCBUZW1wbGF0ZXM7XG5leHBvcnQgbGV0IEFqYXg7XG5leHBvcnQgbGV0IENhY2hlO1xuXG5jb25zdCBCdWlsZGVyID0ge307XG5cbmV4cG9ydCB7IEJ1aWxkZXIgfTtcblxualF1ZXJ5KCBmdW5jdGlvbigpIHtcblx0VGVtcGxhdGVzID0gV1BGb3Jtcy5BZG1pbi5CdWlsZGVyLlRlbXBsYXRlcztcblx0QWpheCA9IFdQRm9ybXMuQWRtaW4uQnVpbGRlci5BamF4O1xuXHRDYWNoZSA9IFdQRm9ybXMuQWRtaW4uQnVpbGRlci5Qcm92aWRlcnMuY2FjaGU7XG59ICk7XG4iXSwibWFwcGluZ3MiOiI7Ozs7OztBQU9BLElBQUFBLE1BQUEsR0FBQUMsT0FBQTtBQVBBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ08sSUFBTUMsV0FBVyxHQUFBQyxPQUFBLENBQUFELFdBQUEsR0FBRyxrQkFBa0I7O0FBRTdDO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDTyxJQUFNRSxlQUFlLEdBQUFELE9BQUEsQ0FBQUMsZUFBQSxHQUFHO0VBQzlCQyxRQUFRLEVBQUVDLGVBQWUsQ0FBQ0MsaUJBQWlCO0VBQzNDO0VBQ0FDLFlBQVksRUFBRUYsZUFBZSxDQUFDRyxxQkFBcUI7RUFD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
},{"./utils.js":14}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.createState = createState;
var _constants = require("./constants.js");
var state = {};

/**
 * Retrieves the current value of a specified property for a given connection.
 * If the property does not exist, initializes it with the provided default value.
 *
 * @since 1.0.0
 *
 * @param {string} connectionId The unique identifier for the connection.
 * @param {string} propertyName The name of the property to retrieve or initialize.
 * @param {*}      defaultValue The default value to set if the property does not exist.
 *
 * @return {*} The current value of the specified property.
 */
function getCurrentValue(connectionId, propertyName, defaultValue) {
  var _state$connectionId, _state$connectionId$p;
  state[connectionId] = (_state$connectionId = state[connectionId]) !== null && _state$connectionId !== void 0 ? _state$connectionId : {};
  state[connectionId][propertyName] = (_state$connectionId$p = state[connectionId][propertyName]) !== null && _state$connectionId$p !== void 0 ? _state$connectionId$p : defaultValue;
  return state[connectionId][propertyName];
}

/**
 * Sets a value for a specific property associated with a given connection ID.
 *
 * @since 1.0.0
 *
 * @param {string} connectionId The ID of the connection for which the value is being set.
 * @param {string} propertyName The name of the property to be updated.
 * @param {*}      value        The value to be assigned to the specified property.
 */
function setValue(connectionId, propertyName, value) {
  var _state$connectionId2;
  state[connectionId] = (_state$connectionId2 = state[connectionId]) !== null && _state$connectionId2 !== void 0 ? _state$connectionId2 : {};
  state[connectionId][propertyName] = value;
}

/**
 * A utility function that provides a mechanism to get and set the state for a specific property.
 * It returns an array with a getter function and a setter function for managing state values.
 *
 * @since 1.0.0
 *
 * @param {string} propertyName        The name of the property to store and retrieve the state.
 * @param {*}      [defaultValue=true] The default value to return if the property state is not yet set.
 *
 * @return {Array<Function>} Returns an array containing a getter function and a setter function.
 */
function createState(propertyName) {
  var defaultValue = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  return [function ($connectionElement) {
    var connectionId = _constants.Builder.app.getConnectionId($connectionElement);
    return getCurrentValue(connectionId, propertyName, defaultValue);
  }, function ($connectionElement, value) {
    var connectionId = _constants.Builder.app.getConnectionId($connectionElement);
    setValue(connectionId, propertyName, value);
  }];
}
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsInN0YXRlIiwiZ2V0Q3VycmVudFZhbHVlIiwiY29ubmVjdGlvbklkIiwicHJvcGVydHlOYW1lIiwiZGVmYXVsdFZhbHVlIiwiX3N0YXRlJGNvbm5lY3Rpb25JZCIsIl9zdGF0ZSRjb25uZWN0aW9uSWQkcCIsInNldFZhbHVlIiwidmFsdWUiLCJfc3RhdGUkY29ubmVjdGlvbklkMiIsImNyZWF0ZVN0YXRlIiwiYXJndW1lbnRzIiwibGVuZ3RoIiwidW5kZWZpbmVkIiwiJGNvbm5lY3Rpb25FbGVtZW50IiwiQnVpbGRlciIsImFwcCIsImdldENvbm5lY3Rpb25JZCJdLCJzb3VyY2VzIjpbImNyZWF0ZVN0YXRlLmpzIl0sInNvdXJjZXNDb250ZW50IjpbImltcG9ydCB7IEJ1aWxkZXIgfSBmcm9tICcuL2NvbnN0YW50cy5qcyc7XG5cbmNvbnN0IHN0YXRlID0ge307XG5cbi8qKlxuICogUmV0cmlldmVzIHRoZSBjdXJyZW50IHZhbHVlIG9mIGEgc3BlY2lmaWVkIHByb3BlcnR5IGZvciBhIGdpdmVuIGNvbm5lY3Rpb24uXG4gKiBJZiB0aGUgcHJvcGVydHkgZG9lcyBub3QgZXhpc3QsIGluaXRpYWxpemVzIGl0IHdpdGggdGhlIHByb3ZpZGVkIGRlZmF1bHQgdmFsdWUuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtzdHJpbmd9IGNvbm5lY3Rpb25JZCBUaGUgdW5pcXVlIGlkZW50aWZpZXIgZm9yIHRoZSBjb25uZWN0aW9uLlxuICogQHBhcmFtIHtzdHJpbmd9IHByb3BlcnR5TmFtZSBUaGUgbmFtZSBvZiB0aGUgcHJvcGVydHkgdG8gcmV0cmlldmUgb3IgaW5pdGlhbGl6ZS5cbiAqIEBwYXJhbSB7Kn0gICAgICBkZWZhdWx0VmFsdWUgVGhlIGRlZmF1bHQgdmFsdWUgdG8gc2V0IGlmIHRoZSBwcm9wZXJ0eSBkb2VzIG5vdCBleGlzdC5cbiAqXG4gKiBAcmV0dXJuIHsqfSBUaGUgY3VycmVudCB2YWx1ZSBvZiB0aGUgc3BlY2lmaWVkIHByb3BlcnR5LlxuICovXG5mdW5jdGlvbiBnZXRDdXJyZW50VmFsdWUoIGNvbm5lY3Rpb25JZCwgcHJvcGVydHlOYW1lLCBkZWZhdWx0VmFsdWUgKSB7XG5cdHN0YXRlWyBjb25uZWN0aW9uSWQgXSA9IHN0YXRlWyBjb25uZWN0aW9uSWQgXSA/PyB7fTtcblx0c3RhdGVbIGNvbm5lY3Rpb25JZCBdWyBwcm9wZXJ0eU5hbWUgXSA9IHN0YXRlWyBjb25uZWN0aW9uSWQgXVsgcHJvcGVydHlOYW1lIF0gPz8gZGVmYXVsdFZhbHVlO1xuXG5cdHJldHVybiBzdGF0ZVsgY29ubmVjdGlvbklkIF1bIHByb3BlcnR5TmFtZSBdO1xufVxuXG4vKipcbiAqIFNldHMgYSB2YWx1ZSBmb3IgYSBzcGVjaWZpYyBwcm9wZXJ0eSBhc3NvY2lhdGVkIHdpdGggYSBnaXZlbiBjb25uZWN0aW9uIElELlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBjb25uZWN0aW9uSWQgVGhlIElEIG9mIHRoZSBjb25uZWN0aW9uIGZvciB3aGljaCB0aGUgdmFsdWUgaXMgYmVpbmcgc2V0LlxuICogQHBhcmFtIHtzdHJpbmd9IHByb3BlcnR5TmFtZSBUaGUgbmFtZSBvZiB0aGUgcHJvcGVydHkgdG8gYmUgdXBkYXRlZC5cbiAqIEBwYXJhbSB7Kn0gICAgICB2YWx1ZSAgICAgICAgVGhlIHZhbHVlIHRvIGJlIGFzc2lnbmVkIHRvIHRoZSBzcGVjaWZpZWQgcHJvcGVydHkuXG4gKi9cbmZ1bmN0aW9uIHNldFZhbHVlKCBjb25uZWN0aW9uSWQsIHByb3BlcnR5TmFtZSwgdmFsdWUgKSB7XG5cdHN0YXRlWyBjb25uZWN0aW9uSWQgXSA9IHN0YXRlWyBjb25uZWN0aW9uSWQgXSA/PyB7fTtcblx0c3RhdGVbIGNvbm5lY3Rpb25JZCBdWyBwcm9wZXJ0eU5hbWUgXSA9IHZhbHVlO1xufVxuXG4vKipcbiAqIEEgdXRpbGl0eSBmdW5jdGlvbiB0aGF0IHByb3ZpZGVzIGEgbWVjaGFuaXNtIHRvIGdldCBhbmQgc2V0IHRoZSBzdGF0ZSBmb3IgYSBzcGVjaWZpYyBwcm9wZXJ0eS5cbiAqIEl0IHJldHVybnMgYW4gYXJyYXkgd2l0aCBhIGdldHRlciBmdW5jdGlvbiBhbmQgYSBzZXR0ZXIgZnVuY3Rpb24gZm9yIG1hbmFnaW5nIHN0YXRlIHZhbHVlcy5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge3N0cmluZ30gcHJvcGVydHlOYW1lICAgICAgICBUaGUgbmFtZSBvZiB0aGUgcHJvcGVydHkgdG8gc3RvcmUgYW5kIHJldHJpZXZlIHRoZSBzdGF0ZS5cbiAqIEBwYXJhbSB7Kn0gICAgICBbZGVmYXVsdFZhbHVlPXRydWVdIFRoZSBkZWZhdWx0IHZhbHVlIHRvIHJldHVybiBpZiB0aGUgcHJvcGVydHkgc3RhdGUgaXMgbm90IHlldCBzZXQuXG4gKlxuICogQHJldHVybiB7QXJyYXk8RnVuY3Rpb24+fSBSZXR1cm5zIGFuIGFycmF5IGNvbnRhaW5pbmcgYSBnZXR0ZXIgZnVuY3Rpb24gYW5kIGEgc2V0dGVyIGZ1bmN0aW9uLlxuICovXG5leHBvcnQgZnVuY3Rpb24gY3JlYXRlU3RhdGUoIHByb3BlcnR5TmFtZSwgZGVmYXVsdFZhbHVlID0gdHJ1ZSApIHtcblx0cmV0dXJuIFtcblx0XHQoICRjb25uZWN0aW9uRWxlbWVudCApID0+IHtcblx0XHRcdGNvbnN0IGNvbm5lY3Rpb25JZCA9IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25JZCggJGNvbm5lY3Rpb25FbGVtZW50ICk7XG5cblx0XHRcdHJldHVybiBnZXRDdXJyZW50VmFsdWUoIGNvbm5lY3Rpb25JZCwgcHJvcGVydHlOYW1lLCBkZWZhdWx0VmFsdWUgKTtcblx0XHR9LFxuXHRcdCggJGNvbm5lY3Rpb25FbGVtZW50LCB2YWx1ZSApID0+IHtcblx0XHRcdGNvbnN0IGNvbm5lY3Rpb25JZCA9IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25JZCggJGNvbm5lY3Rpb25FbGVtZW50ICk7XG5cblx0XHRcdHNldFZhbHVlKCBjb25uZWN0aW9uSWQsIHByb3BlcnR5TmFtZSwgdmFsdWUgKTtcblx0XHR9LFxuXHRdO1xufVxuIl0sIm1hcHBpbmdzIjoiOzs7Ozs7QUFBQSxJQUFBQSxVQUFBLEdBQUFDLE9BQUE7QUFFQSxJQUFNQyxLQUFLLEdBQUcsQ0FBQyxDQUFDOztBQUVoQjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxTQUFTQyxlQUFlQSxDQUFFQyxZQUFZLEVBQUVDLFlBQVksRUFBRUMsWUFBWSxFQUFHO0VBQUEsSUFBQUMsbUJBQUEsRUFBQUMscUJBQUE7RUFDcEVOLEtBQUssQ0FBRUUsWUFBWSxDQUFFLElBQUFHLG1CQUFBLEdBQUdMLEtBQUssQ0FBRUUsWUFBWSxDQUFFLGNBQUFHLG1CQUFBLGNBQUFBLG1CQUFBLEdBQUksQ0FBQyxDQUFDO0VBQ25ETCxLQUFLLENBQUVFLFlBQVksQ0FBRSxDQUFFQyxZQUFZLENBQUUsSUFBQUcscUJBQUEsR0FBR04sS0FBSyxDQUFFRSxZQUFZLENBQUUsQ0FBRUMsWUFBWSxDQUFFLGNBQUFHLHFCQUFBLGNBQUFBLHFCQUFBLEdBQUlGLFlBQVk7RUFFN0YsT0FBT0osS0FBSyxDQUFFRSxZQUFZLENBQUUsQ0FBRUMsWUFBWSxDQUFFO0FBQzdDOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLFNBQVNJLFFBQVFBLENBQUVMLFlBQVksRUFBRUMsWUFBWSxFQUFFSyxLQUFLLEVBQUc7RUFBQSxJQUFBQyxvQkFBQTtFQUN0RFQsS0FBSyxDQUFFRSxZQUFZLENBQUUsSUFBQU8sb0JBQUEsR0FBR1QsS0FBSyxDQUFFRSxZQUFZLENBQUUsY0FBQU8sb0JBQUEsY0FBQUEsb0JBQUEsR0FBSSxDQUFDLENBQUM7RUFDbkRULEtBQUssQ0FBRUUsWUFBWSxDQUFFLENBQUVDLFlBQVksQ0FBRSxHQUFHSyxLQUFLO0FBQzlDOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDTyxTQUFTRSxXQUFXQSxDQUFFUCxZQUFZLEVBQXdCO0VBQUEsSUFBdEJDLFlBQVksR0FBQU8sU0FBQSxDQUFBQyxNQUFBLFFBQUFELFNBQUEsUUFBQUUsU0FBQSxHQUFBRixTQUFBLE1BQUcsSUFBSTtFQUM3RCxPQUFPLENBQ04sVUFBRUcsa0JBQWtCLEVBQU07SUFDekIsSUFBTVosWUFBWSxHQUFHYSxrQkFBTyxDQUFDQyxHQUFHLENBQUNDLGVBQWUsQ0FBRUgsa0JBQW1CLENBQUM7SUFFdEUsT0FBT2IsZUFBZSxDQUFFQyxZQUFZLEVBQUVDLFlBQVksRUFBRUMsWUFBYSxDQUFDO0VBQ25FLENBQUMsRUFDRCxVQUFFVSxrQkFBa0IsRUFBRU4sS0FBSyxFQUFNO0lBQ2hDLElBQU1OLFlBQVksR0FBR2Esa0JBQU8sQ0FBQ0MsR0FBRyxDQUFDQyxlQUFlLENBQUVILGtCQUFtQixDQUFDO0lBRXRFUCxRQUFRLENBQUVMLFlBQVksRUFBRUMsWUFBWSxFQUFFSyxLQUFNLENBQUM7RUFDOUMsQ0FBQyxDQUNEO0FBQ0YiLCJpZ25vcmVMaXN0IjpbXX0=
},{"./constants.js":4}],6:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.getRenderData = getRenderData;
exports.initDropbox = initDropbox;
exports.notifyAboutRequiredConnection = notifyAboutRequiredConnection;
exports.selectors = void 0;
var _constants = require("./constants");
var _exportTo = require("./export.to.js");
var _createState3 = require("./createState");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; } /* global WPForms, wpf, WPFormsBuilder, wpforms_builder, wpformsEntryAutomationBuilderVars */
var $ = jQuery;
var DropboxProvider = WPForms.Admin.Builder.Providers.Dropbox;
var hasShownAlert = false;

/**
 * Checks if the account associated with the given element has updated scopes.
 *
 * @since 1.0.0
 *
 * @param {Object} $element The jQuery element to check for associated account scopes.
 *
 * @return {boolean} True if the account has updated scopes, false otherwise.
 */
function isAccountWithScopes($element) {
  var $task = $element.closest(_constants.selectors.task);
  var $accountField = $(DropboxProvider.selectors.accountField, $task);
  var accountId = $accountField.val();
  if (!accountId) {
    return true;
  }
  var accountWithScope = _constants.Cache.getById(_constants.ENTITY_SLUG, 'accountsWithScopes', accountId);
  return !!(accountWithScope !== null && accountWithScope !== void 0 && accountWithScope.updated_scopes);
}

/**
 * Updates the scopes of a specified account by marking it as having updated scopes.
 *
 * @since 1.0.0
 *
 * @param {string|number} accountId The ID of the account to update scopes for.
 */
function updateAccountScopes(accountId) {
  var accountWithScope = _constants.Cache.getById(_constants.ENTITY_SLUG, 'accountsWithScopes', accountId);
  accountWithScope.updated_scopes = true; // eslint-disable-line camelcase
}

/**
 * Saves the current form and redirects if the form is already saved.
 * If not saved, it saves the form and updates the exit URL.
 *
 * @since 1.0.0
 */
function saveForm() {
  var url = wpformsEntryAutomationBuilderVars.dropboxAuth;
  if (WPFormsBuilder.formIsSaved()) {
    window.location.href = url;
    return;
  }

  // eslint-disable-next-line camelcase
  wpforms_builder.exit_url = url;
  // Avoid showing alert modal.
  hasShownAlert = true;

  // Disable confirmation modal.
  WPFormsBuilder.setCloseConfirmation(false);
  WPFormsBuilder.formSave(true);
}

/**
 * Show permissions update modal for Dropbox.
 *
 * @since 1.0.0
 *
 * @param {Object} $accountField The Dropbox field select.
 */
function showPermissionsModal($accountField) {
  var accountId = $accountField.val();
  $.confirm({
    title: wpformsEntryAutomationBuilderVars.dropboxPermissionModal.title,
    content: wpformsEntryAutomationBuilderVars.dropboxPermissionModal.content,
    icon: 'fa fa-exclamation-circle',
    type: 'orange',
    buttons: {
      confirm: {
        text: wpforms_builder.ok,
        btnClass: 'btn-confirm',
        keys: ['enter'],
        action: function action() {
          // Send AJAX request to get authorization URL with new_scopes parameter.
          var data = {
            task: "dropbox_reconnect",
            accountId: accountId
          };
          _constants.Ajax.request(_constants.ENTITY_SLUG, {
            data: data
          }).then(function (response) {
            if (!response.success) {
              return;
            }

            // Save the form and redirect to the authorization URL.
            saveForm();
          }).finally(function () {
            return updateAccountScopes(accountId);
          });
        }
      },
      cancel: {
        text: wpforms_builder.cancel,
        btnClass: 'btn-cancel',
        action: function action() {
          $accountField.val('').trigger('change');
        }
      }
    }
  });
}
var selectors = exports.selectors = {
  removeConnection: '.wpforms-builder-dropbox-connection-remove'
};
var _createState = (0, _createState3.createState)('showDropboxAddConnectionButton', true),
  _createState2 = _slicedToArray(_createState, 2),
  isShowAddConnectionButton = _createState2[0],
  setShowAddConnectionButton = _createState2[1];
var initialLoad = false;

/**
 * Loads and populates fields in the task container based on the selected account and folders.
 * This method retrieves data from the cache and updates the field container with the relevant template.
 *
 * @since 1.0.0
 */
function loadFields() {
  // eslint-disable-line complexity
  var $accountField = $(this);
  var $task = $accountField.closest(_constants.selectors.task);
  var $fieldsContainer = $(DropboxProvider.selectors.actionData, $task);
  var accountWithScope = _constants.Cache.getById(_constants.ENTITY_SLUG, 'accountsWithScopes', $accountField.val());
  if (!$accountField.val() || accountWithScope === null) {
    $fieldsContainer.html('');
    return;
  }

  // Check if onDuplicate has selected value = "add".
  var $onDuplicate = $(_constants.selectors.onDuplicate, $task);
  if ($onDuplicate.val() === 'add' && !isAccountWithScopes($accountField)) {
    showPermissionsModal($accountField);
    return;
  }
  var folders = _constants.Cache.getById(_constants.ENTITY_SLUG, 'dropbox_folders', $accountField.val()) || {};
  var template = _constants.Templates.get("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export-to-dropbox-fields"));
  $fieldsContainer.html(template({
    connection: _constants.Builder.app.getConnectionData($task).connection,
    folders: folders,
    fields: wpf.getFields(false, true),
    formName: $('#wpforms-panel-field-settings-form_title').val(),
    provider: _constants.ENTITY_SLUG
  }));
  if (initialLoad) {
    return;
  }
  var connectionID = _constants.Builder.app.getConnectionId($task);
  $fieldsContainer.closest('.wpforms-panel').trigger('connectionGeneralSettingsRendered', [_constants.ENTITY_SLUG, connectionID]);
  initialLoad = true;
}

/**
 * Callback function triggered when account data is loaded.
 * It re-renders the export-related options for each export element found.
 *
 * @since 1.0.0
 */
function onAccountsLoaded() {
  var $tasksExportOptions = $(_constants.selectors.exportTo, _constants.$elements.$tasks);
  initialLoad = false;
  $tasksExportOptions.each(function (_, exportTo) {
    // Re-render export-related options.
    _exportTo.exportToSwitchOptions.call($(exportTo));
  });
}

/**
 * Loads account scopes by making an AJAX request and updates cache with the retrieved data.
 *
 * @since 1.0.0
 *
 * @return {Object} Returns the AJAX request object, or undefined if the response is invalid.
 */
function loadAccountsScopes() {
  var data = {
    task: "dropbox_load_accounts_scopes"
  };
  return _constants.Ajax.request(_constants.ENTITY_SLUG, {
    data: data
  }).then(function (response) {
    var _response$data;
    if (!response.success || _typeof((_response$data = response.data) === null || _response$data === void 0 ? void 0 : _response$data.accounts) !== 'object') {
      return;
    }
    _constants.Cache.set(_constants.ENTITY_SLUG, 'accountsWithScopes', response.data.accounts);
  });
}

/**
 * Handles the addition of a new connection triggered by the user.
 * The method prevents the default event behavior, gets the associated task,
 * hides the "add connection" button, and re-renders export-related options in the UI.
 *
 * @since 1.0.0
 *
 * @param {Event} event The event object triggered by the "add connection" action.
 */
function onAddConnection(event) {
  event.preventDefault();
  var $button = $(this);
  var $task = $button.closest(_constants.selectors.task);
  setShowAddConnectionButton($button, false);

  // Re-render export-related options.
  _exportTo.exportToSwitchOptions.call($(_constants.selectors.exportTo, $task));
}

/**
 * Handles the event triggered when a connection is removed. It prevents the default behavior,
 * clears the associated account field, updates the button state, and re-renders export-related options.
 *
 * @since 1.0.0
 *
 * @param {Event} event The event object triggered by the remove connection action.
 */
function onRemoveConnection(event) {
  event.preventDefault();
  var $button = $(this);
  var $task = $button.closest(_constants.selectors.task);
  var connectionId = _constants.Builder.app.getConnectionId($task);
  var _Builder$app$getConne = _constants.Builder.app.getConnectionData($task),
    connection = _Builder$app$getConne.connection;
  connection.dropbox = {};
  _constants.Cache.addTo(_constants.ENTITY_SLUG, 'connections', connectionId, connection);
  setShowAddConnectionButton($button, true);

  // Re-render export-related options.
  _exportTo.exportToSwitchOptions.call($(_constants.selectors.exportTo, $task));
}

/**
 * Handles the "Add Account" button click event.
 * Prevents the default action, ensures the form is saved,
 * and redirects to the specified URL.
 *
 * @since 1.0.0
 *
 * @param {Event} event The event object triggered by the button click.
 */
function onAddAccount(event) {
  event.preventDefault();
  saveForm();
}

/**
 * Retrieves the rendering data for the specified container, including account information
 * and state for displaying the "Add Connection" button.
 *
 * @since 1.0.0
 *
 * @param {HTMLElement} $container The container element for which the rendering data is being prepared.
 *
 * @return {Object} An object containing the rendering data, including accounts and the "Add Connection" button state.
 */
function getRenderData($container) {
  var accounts = _constants.Cache.get(DropboxProvider.provider, 'accounts');
  return {
    accounts: accounts,
    showAddConnectionButton: isShowAddConnectionButton($container)
  };
}

/**
 * Handles the change event for the onDuplicate selector.
 * If the value changes to "add" and exportTo is "dropbox", show the permissions modal.
 *
 * @since 1.0.0
 */
function onDuplicateChange() {
  var $onDuplicate = $(this);
  var $task = $onDuplicate.closest(_constants.selectors.task);
  var $exportTo = $(_constants.selectors.exportTo, $task);
  var $accountField = $(DropboxProvider.selectors.accountField, $task);
  if ($onDuplicate.val() === 'add' && $exportTo.val() === 'dropbox' && !isAccountWithScopes($exportTo)) {
    showPermissionsModal($accountField);
  }
}

/**
 * Notifies the user about the required connection if the "export to" field
 * is set to Dropbox and the account field is not present.
 *
 * This method checks the task-specific configuration and displays an alert
 * if the Dropbox connection is required but not set up.
 *
 * @since 1.0.0
 *
 * @param {HTMLElement} task The DOM element representing the task context.
 */
function notifyAboutRequiredConnection(task) {
  var $task = $(task);
  var $exportTo = $(_constants.selectors.exportTo, $task);
  var $accountField = $(DropboxProvider.selectors.accountField, $task);
  if ($exportTo.val() !== 'dropbox' || $accountField.length || hasShownAlert) {
    return;
  }
  hasShownAlert = true;
  $.alert({
    title: wpforms_builder.heads_up,
    content: wpformsEntryAutomationBuilderVars.dropboxRequiredConnection,
    icon: 'fa fa-exclamation-circle',
    type: 'red',
    buttons: {
      confirm: {
        text: wpforms_builder.ok,
        btnClass: 'btn-confirm',
        keys: ['enter']
      }
    }
  });
}

/**
 * Initializes the Dropbox integration by setting up event listeners
 * and adding necessary templates for the automation builder.
 *
 * This method binds a change event to the accountField selector and
 * registers templates required for exporting to Dropbox.
 *
 * @since 1.0.0
 */
function initDropbox() {
  _constants.$elements.$tasks.on('change', DropboxProvider.selectors.accountField, loadFields).on('click', "".concat(_constants.selectors.addConnection, ".dropbox"), onAddConnection).on('click', "".concat(_constants.selectors.addAccount, ".dropbox"), onAddAccount).on('click', selectors.removeConnection, onRemoveConnection).on('change', _constants.selectors.onDuplicate, onDuplicateChange);
  DropboxProvider.$elements.$connections.on('connectionsDataLoaded', onAccountsLoaded);
  _constants.Templates.add(["wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export-to-dropbox"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export-to-dropbox-fields")]);
  var promise = loadAccountsScopes();
  _exportTo.destinationsData.dropbox = getRenderData;
  _exportTo.destinationsInit.dropbox = function ($container) {
    var $accountField = $(DropboxProvider.selectors.accountField, $container);
    loadFields.call($accountField);
    promise.finally(function () {
      return loadFields.call($accountField);
    });
  };
}
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIl9leHBvcnRUbyIsIl9jcmVhdGVTdGF0ZTMiLCJfdHlwZW9mIiwibyIsIlN5bWJvbCIsIml0ZXJhdG9yIiwiY29uc3RydWN0b3IiLCJwcm90b3R5cGUiLCJfc2xpY2VkVG9BcnJheSIsInIiLCJlIiwiX2FycmF5V2l0aEhvbGVzIiwiX2l0ZXJhYmxlVG9BcnJheUxpbWl0IiwiX3Vuc3VwcG9ydGVkSXRlcmFibGVUb0FycmF5IiwiX25vbkl0ZXJhYmxlUmVzdCIsIlR5cGVFcnJvciIsImEiLCJfYXJyYXlMaWtlVG9BcnJheSIsInQiLCJ0b1N0cmluZyIsImNhbGwiLCJzbGljZSIsIm5hbWUiLCJBcnJheSIsImZyb20iLCJ0ZXN0IiwibGVuZ3RoIiwibiIsImwiLCJpIiwidSIsImYiLCJuZXh0IiwiT2JqZWN0IiwiZG9uZSIsInB1c2giLCJ2YWx1ZSIsInJldHVybiIsImlzQXJyYXkiLCIkIiwialF1ZXJ5IiwiRHJvcGJveFByb3ZpZGVyIiwiV1BGb3JtcyIsIkFkbWluIiwiQnVpbGRlciIsIlByb3ZpZGVycyIsIkRyb3Bib3giLCJoYXNTaG93bkFsZXJ0IiwiaXNBY2NvdW50V2l0aFNjb3BlcyIsIiRlbGVtZW50IiwiJHRhc2siLCJjbG9zZXN0IiwiY29yZVNlbGVjdG9ycyIsInRhc2siLCIkYWNjb3VudEZpZWxkIiwic2VsZWN0b3JzIiwiYWNjb3VudEZpZWxkIiwiYWNjb3VudElkIiwidmFsIiwiYWNjb3VudFdpdGhTY29wZSIsIkNhY2hlIiwiZ2V0QnlJZCIsIkVOVElUWV9TTFVHIiwidXBkYXRlZF9zY29wZXMiLCJ1cGRhdGVBY2NvdW50U2NvcGVzIiwic2F2ZUZvcm0iLCJ1cmwiLCJ3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMiLCJkcm9wYm94QXV0aCIsIldQRm9ybXNCdWlsZGVyIiwiZm9ybUlzU2F2ZWQiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImhyZWYiLCJ3cGZvcm1zX2J1aWxkZXIiLCJleGl0X3VybCIsInNldENsb3NlQ29uZmlybWF0aW9uIiwiZm9ybVNhdmUiLCJzaG93UGVybWlzc2lvbnNNb2RhbCIsImNvbmZpcm0iLCJ0aXRsZSIsImRyb3Bib3hQZXJtaXNzaW9uTW9kYWwiLCJjb250ZW50IiwiaWNvbiIsInR5cGUiLCJidXR0b25zIiwidGV4dCIsIm9rIiwiYnRuQ2xhc3MiLCJrZXlzIiwiYWN0aW9uIiwiZGF0YSIsIkFqYXgiLCJyZXF1ZXN0IiwidGhlbiIsInJlc3BvbnNlIiwic3VjY2VzcyIsImZpbmFsbHkiLCJjYW5jZWwiLCJ0cmlnZ2VyIiwiZXhwb3J0cyIsInJlbW92ZUNvbm5lY3Rpb24iLCJfY3JlYXRlU3RhdGUiLCJjcmVhdGVTdGF0ZSIsIl9jcmVhdGVTdGF0ZTIiLCJpc1Nob3dBZGRDb25uZWN0aW9uQnV0dG9uIiwic2V0U2hvd0FkZENvbm5lY3Rpb25CdXR0b24iLCJpbml0aWFsTG9hZCIsImxvYWRGaWVsZHMiLCIkZmllbGRzQ29udGFpbmVyIiwiYWN0aW9uRGF0YSIsImh0bWwiLCIkb25EdXBsaWNhdGUiLCJvbkR1cGxpY2F0ZSIsImZvbGRlcnMiLCJ0ZW1wbGF0ZSIsIlRlbXBsYXRlcyIsImdldCIsImNvbmNhdCIsImNvbm5lY3Rpb24iLCJhcHAiLCJnZXRDb25uZWN0aW9uRGF0YSIsImZpZWxkcyIsIndwZiIsImdldEZpZWxkcyIsImZvcm1OYW1lIiwicHJvdmlkZXIiLCJjb25uZWN0aW9uSUQiLCJnZXRDb25uZWN0aW9uSWQiLCJvbkFjY291bnRzTG9hZGVkIiwiJHRhc2tzRXhwb3J0T3B0aW9ucyIsImV4cG9ydFRvIiwiJGVsZW1lbnRzIiwiJHRhc2tzIiwiZWFjaCIsIl8iLCJleHBvcnRUb1N3aXRjaE9wdGlvbnMiLCJsb2FkQWNjb3VudHNTY29wZXMiLCJfcmVzcG9uc2UkZGF0YSIsImFjY291bnRzIiwic2V0Iiwib25BZGRDb25uZWN0aW9uIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsIiRidXR0b24iLCJvblJlbW92ZUNvbm5lY3Rpb24iLCJjb25uZWN0aW9uSWQiLCJfQnVpbGRlciRhcHAkZ2V0Q29ubmUiLCJkcm9wYm94IiwiYWRkVG8iLCJvbkFkZEFjY291bnQiLCJnZXRSZW5kZXJEYXRhIiwiJGNvbnRhaW5lciIsInNob3dBZGRDb25uZWN0aW9uQnV0dG9uIiwib25EdXBsaWNhdGVDaGFuZ2UiLCIkZXhwb3J0VG8iLCJub3RpZnlBYm91dFJlcXVpcmVkQ29ubmVjdGlvbiIsImFsZXJ0IiwiaGVhZHNfdXAiLCJkcm9wYm94UmVxdWlyZWRDb25uZWN0aW9uIiwiaW5pdERyb3Bib3giLCJvbiIsImFkZENvbm5lY3Rpb24iLCJhZGRBY2NvdW50IiwiJGNvbm5lY3Rpb25zIiwiYWRkIiwicHJvbWlzZSIsImRlc3RpbmF0aW9uc0RhdGEiLCJkZXN0aW5hdGlvbnNJbml0Il0sInNvdXJjZXMiOlsiZHJvcGJveC5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKiBnbG9iYWwgV1BGb3Jtcywgd3BmLCBXUEZvcm1zQnVpbGRlciwgd3Bmb3Jtc19idWlsZGVyLCB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMgKi9cblxuaW1wb3J0IHsgJGVsZW1lbnRzLCBCdWlsZGVyLCBFTlRJVFlfU0xVRywgc2VsZWN0b3JzIGFzIGNvcmVTZWxlY3RvcnMsIFRlbXBsYXRlcywgQ2FjaGUsIEFqYXggfSBmcm9tICcuL2NvbnN0YW50cyc7XG5pbXBvcnQgeyBkZXN0aW5hdGlvbnNEYXRhLCBkZXN0aW5hdGlvbnNJbml0LCBleHBvcnRUb1N3aXRjaE9wdGlvbnMgfSBmcm9tICcuL2V4cG9ydC50by5qcyc7XG5pbXBvcnQgeyBjcmVhdGVTdGF0ZSB9IGZyb20gJy4vY3JlYXRlU3RhdGUnO1xuXG5jb25zdCAkID0galF1ZXJ5O1xuY29uc3QgRHJvcGJveFByb3ZpZGVyID0gV1BGb3Jtcy5BZG1pbi5CdWlsZGVyLlByb3ZpZGVycy5Ecm9wYm94O1xuXG5sZXQgaGFzU2hvd25BbGVydCA9IGZhbHNlO1xuXG4vKipcbiAqIENoZWNrcyBpZiB0aGUgYWNjb3VudCBhc3NvY2lhdGVkIHdpdGggdGhlIGdpdmVuIGVsZW1lbnQgaGFzIHVwZGF0ZWQgc2NvcGVzLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSAkZWxlbWVudCBUaGUgalF1ZXJ5IGVsZW1lbnQgdG8gY2hlY2sgZm9yIGFzc29jaWF0ZWQgYWNjb3VudCBzY29wZXMuXG4gKlxuICogQHJldHVybiB7Ym9vbGVhbn0gVHJ1ZSBpZiB0aGUgYWNjb3VudCBoYXMgdXBkYXRlZCBzY29wZXMsIGZhbHNlIG90aGVyd2lzZS5cbiAqL1xuZnVuY3Rpb24gaXNBY2NvdW50V2l0aFNjb3BlcyggJGVsZW1lbnQgKSB7XG5cdGNvbnN0ICR0YXNrID0gJGVsZW1lbnQuY2xvc2VzdCggY29yZVNlbGVjdG9ycy50YXNrICk7XG5cdGNvbnN0ICRhY2NvdW50RmllbGQgPSAkKCBEcm9wYm94UHJvdmlkZXIuc2VsZWN0b3JzLmFjY291bnRGaWVsZCwgJHRhc2sgKTtcblx0Y29uc3QgYWNjb3VudElkID0gJGFjY291bnRGaWVsZC52YWwoKTtcblxuXHRpZiAoICEgYWNjb3VudElkICkge1xuXHRcdHJldHVybiB0cnVlO1xuXHR9XG5cblx0Y29uc3QgYWNjb3VudFdpdGhTY29wZSA9IENhY2hlLmdldEJ5SWQoIEVOVElUWV9TTFVHLCAnYWNjb3VudHNXaXRoU2NvcGVzJywgYWNjb3VudElkICk7XG5cblx0cmV0dXJuICEhIGFjY291bnRXaXRoU2NvcGU/LnVwZGF0ZWRfc2NvcGVzO1xufVxuXG4vKipcbiAqIFVwZGF0ZXMgdGhlIHNjb3BlcyBvZiBhIHNwZWNpZmllZCBhY2NvdW50IGJ5IG1hcmtpbmcgaXQgYXMgaGF2aW5nIHVwZGF0ZWQgc2NvcGVzLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfG51bWJlcn0gYWNjb3VudElkIFRoZSBJRCBvZiB0aGUgYWNjb3VudCB0byB1cGRhdGUgc2NvcGVzIGZvci5cbiAqL1xuZnVuY3Rpb24gdXBkYXRlQWNjb3VudFNjb3BlcyggYWNjb3VudElkICkge1xuXHRjb25zdCBhY2NvdW50V2l0aFNjb3BlID0gQ2FjaGUuZ2V0QnlJZCggRU5USVRZX1NMVUcsICdhY2NvdW50c1dpdGhTY29wZXMnLCBhY2NvdW50SWQgKTtcblxuXHRhY2NvdW50V2l0aFNjb3BlLnVwZGF0ZWRfc2NvcGVzID0gdHJ1ZTsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBjYW1lbGNhc2Vcbn1cblxuLyoqXG4gKiBTYXZlcyB0aGUgY3VycmVudCBmb3JtIGFuZCByZWRpcmVjdHMgaWYgdGhlIGZvcm0gaXMgYWxyZWFkeSBzYXZlZC5cbiAqIElmIG5vdCBzYXZlZCwgaXQgc2F2ZXMgdGhlIGZvcm0gYW5kIHVwZGF0ZXMgdGhlIGV4aXQgVVJMLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICovXG5mdW5jdGlvbiBzYXZlRm9ybSgpIHtcblx0Y29uc3QgdXJsID0gd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzLmRyb3Bib3hBdXRoO1xuXG5cdGlmICggV1BGb3Jtc0J1aWxkZXIuZm9ybUlzU2F2ZWQoKSApIHtcblx0XHR3aW5kb3cubG9jYXRpb24uaHJlZiA9IHVybDtcblxuXHRcdHJldHVybjtcblx0fVxuXG5cdC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBjYW1lbGNhc2Vcblx0d3Bmb3Jtc19idWlsZGVyLmV4aXRfdXJsID0gdXJsO1xuXHQvLyBBdm9pZCBzaG93aW5nIGFsZXJ0IG1vZGFsLlxuXHRoYXNTaG93bkFsZXJ0ID0gdHJ1ZTtcblxuXHQvLyBEaXNhYmxlIGNvbmZpcm1hdGlvbiBtb2RhbC5cblx0V1BGb3Jtc0J1aWxkZXIuc2V0Q2xvc2VDb25maXJtYXRpb24oIGZhbHNlICk7XG5cdFdQRm9ybXNCdWlsZGVyLmZvcm1TYXZlKCB0cnVlICk7XG59XG5cbi8qKlxuICogU2hvdyBwZXJtaXNzaW9ucyB1cGRhdGUgbW9kYWwgZm9yIERyb3Bib3guXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtPYmplY3R9ICRhY2NvdW50RmllbGQgVGhlIERyb3Bib3ggZmllbGQgc2VsZWN0LlxuICovXG5mdW5jdGlvbiBzaG93UGVybWlzc2lvbnNNb2RhbCggJGFjY291bnRGaWVsZCApIHtcblx0Y29uc3QgYWNjb3VudElkID0gJGFjY291bnRGaWVsZC52YWwoKTtcblxuXHQkLmNvbmZpcm0oIHtcblx0XHR0aXRsZTogd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzLmRyb3Bib3hQZXJtaXNzaW9uTW9kYWwudGl0bGUsXG5cdFx0Y29udGVudDogd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzLmRyb3Bib3hQZXJtaXNzaW9uTW9kYWwuY29udGVudCxcblx0XHRpY29uOiAnZmEgZmEtZXhjbGFtYXRpb24tY2lyY2xlJyxcblx0XHR0eXBlOiAnb3JhbmdlJyxcblx0XHRidXR0b25zOiB7XG5cdFx0XHRjb25maXJtOiB7XG5cdFx0XHRcdHRleHQ6IHdwZm9ybXNfYnVpbGRlci5vayxcblx0XHRcdFx0YnRuQ2xhc3M6ICdidG4tY29uZmlybScsXG5cdFx0XHRcdGtleXM6IFsgJ2VudGVyJyBdLFxuXHRcdFx0XHRhY3Rpb24oKSB7XG5cdFx0XHRcdFx0Ly8gU2VuZCBBSkFYIHJlcXVlc3QgdG8gZ2V0IGF1dGhvcml6YXRpb24gVVJMIHdpdGggbmV3X3Njb3BlcyBwYXJhbWV0ZXIuXG5cdFx0XHRcdFx0Y29uc3QgZGF0YSA9IHtcblx0XHRcdFx0XHRcdHRhc2s6IGBkcm9wYm94X3JlY29ubmVjdGAsXG5cdFx0XHRcdFx0XHRhY2NvdW50SWQsXG5cdFx0XHRcdFx0fTtcblxuXHRcdFx0XHRcdEFqYXgucmVxdWVzdCggRU5USVRZX1NMVUcsIHsgZGF0YSB9ICkudGhlbiggZnVuY3Rpb24oIHJlc3BvbnNlICkge1xuXHRcdFx0XHRcdFx0aWYgKCAhIHJlc3BvbnNlLnN1Y2Nlc3MgKSB7XG5cdFx0XHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdFx0Ly8gU2F2ZSB0aGUgZm9ybSBhbmQgcmVkaXJlY3QgdG8gdGhlIGF1dGhvcml6YXRpb24gVVJMLlxuXHRcdFx0XHRcdFx0c2F2ZUZvcm0oKTtcblx0XHRcdFx0XHR9ICkuZmluYWxseSggKCkgPT4gdXBkYXRlQWNjb3VudFNjb3BlcyggYWNjb3VudElkICkgKTtcblx0XHRcdFx0fSxcblx0XHRcdH0sXG5cdFx0XHRjYW5jZWw6IHtcblx0XHRcdFx0dGV4dDogd3Bmb3Jtc19idWlsZGVyLmNhbmNlbCxcblx0XHRcdFx0YnRuQ2xhc3M6ICdidG4tY2FuY2VsJyxcblx0XHRcdFx0YWN0aW9uKCkge1xuXHRcdFx0XHRcdCRhY2NvdW50RmllbGQudmFsKCAnJyApLnRyaWdnZXIoICdjaGFuZ2UnICk7XG5cdFx0XHRcdH0sXG5cdFx0XHR9LFxuXHRcdH0sXG5cdH0gKTtcbn1cblxuZXhwb3J0IGNvbnN0IHNlbGVjdG9ycyA9IHtcblx0cmVtb3ZlQ29ubmVjdGlvbjogJy53cGZvcm1zLWJ1aWxkZXItZHJvcGJveC1jb25uZWN0aW9uLXJlbW92ZScsXG59O1xuXG5jb25zdCBbIGlzU2hvd0FkZENvbm5lY3Rpb25CdXR0b24sIHNldFNob3dBZGRDb25uZWN0aW9uQnV0dG9uIF0gPSBjcmVhdGVTdGF0ZSggJ3Nob3dEcm9wYm94QWRkQ29ubmVjdGlvbkJ1dHRvbicsIHRydWUgKTtcblxubGV0IGluaXRpYWxMb2FkID0gZmFsc2U7XG5cbi8qKlxuICogTG9hZHMgYW5kIHBvcHVsYXRlcyBmaWVsZHMgaW4gdGhlIHRhc2sgY29udGFpbmVyIGJhc2VkIG9uIHRoZSBzZWxlY3RlZCBhY2NvdW50IGFuZCBmb2xkZXJzLlxuICogVGhpcyBtZXRob2QgcmV0cmlldmVzIGRhdGEgZnJvbSB0aGUgY2FjaGUgYW5kIHVwZGF0ZXMgdGhlIGZpZWxkIGNvbnRhaW5lciB3aXRoIHRoZSByZWxldmFudCB0ZW1wbGF0ZS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuZnVuY3Rpb24gbG9hZEZpZWxkcygpIHsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBjb21wbGV4aXR5XG5cdGNvbnN0ICRhY2NvdW50RmllbGQgPSAkKCB0aGlzICk7XG5cdGNvbnN0ICR0YXNrID0gJGFjY291bnRGaWVsZC5jbG9zZXN0KCBjb3JlU2VsZWN0b3JzLnRhc2sgKTtcblx0Y29uc3QgJGZpZWxkc0NvbnRhaW5lciA9ICQoIERyb3Bib3hQcm92aWRlci5zZWxlY3RvcnMuYWN0aW9uRGF0YSwgJHRhc2sgKTtcblx0Y29uc3QgYWNjb3VudFdpdGhTY29wZSA9IENhY2hlLmdldEJ5SWQoIEVOVElUWV9TTFVHLCAnYWNjb3VudHNXaXRoU2NvcGVzJywgJGFjY291bnRGaWVsZC52YWwoKSApO1xuXG5cdGlmICggISAkYWNjb3VudEZpZWxkLnZhbCgpIHx8IGFjY291bnRXaXRoU2NvcGUgPT09IG51bGwgKSB7XG5cdFx0JGZpZWxkc0NvbnRhaW5lci5odG1sKCAnJyApO1xuXG5cdFx0cmV0dXJuO1xuXHR9XG5cblx0Ly8gQ2hlY2sgaWYgb25EdXBsaWNhdGUgaGFzIHNlbGVjdGVkIHZhbHVlID0gXCJhZGRcIi5cblx0Y29uc3QgJG9uRHVwbGljYXRlID0gJCggY29yZVNlbGVjdG9ycy5vbkR1cGxpY2F0ZSwgJHRhc2sgKTtcblxuXHRpZiAoICRvbkR1cGxpY2F0ZS52YWwoKSA9PT0gJ2FkZCcgJiYgISBpc0FjY291bnRXaXRoU2NvcGVzKCAkYWNjb3VudEZpZWxkICkgKSB7XG5cdFx0c2hvd1Blcm1pc3Npb25zTW9kYWwoICRhY2NvdW50RmllbGQgKTtcblxuXHRcdHJldHVybjtcblx0fVxuXG5cdGNvbnN0IGZvbGRlcnMgPSBDYWNoZS5nZXRCeUlkKCBFTlRJVFlfU0xVRywgJ2Ryb3Bib3hfZm9sZGVycycsICRhY2NvdW50RmllbGQudmFsKCkgKSB8fCB7fTtcblx0Y29uc3QgdGVtcGxhdGUgPSBUZW1wbGF0ZXMuZ2V0KCBgd3Bmb3Jtcy0keyBFTlRJVFlfU0xVRyB9LWJ1aWxkZXItY29udGVudC10YXNrLWV4cG9ydC10by1kcm9wYm94LWZpZWxkc2AgKTtcblxuXHQkZmllbGRzQ29udGFpbmVyLmh0bWwoIHRlbXBsYXRlKCB7XG5cdFx0Y29ubmVjdGlvbjogQnVpbGRlci5hcHAuZ2V0Q29ubmVjdGlvbkRhdGEoICR0YXNrICkuY29ubmVjdGlvbixcblx0XHRmb2xkZXJzLFxuXHRcdGZpZWxkczogd3BmLmdldEZpZWxkcyggZmFsc2UsIHRydWUgKSxcblx0XHRmb3JtTmFtZTogJCggJyN3cGZvcm1zLXBhbmVsLWZpZWxkLXNldHRpbmdzLWZvcm1fdGl0bGUnICkudmFsKCksXG5cdFx0cHJvdmlkZXI6IEVOVElUWV9TTFVHLFxuXHR9ICkgKTtcblxuXHRpZiAoIGluaXRpYWxMb2FkICkge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdGNvbnN0IGNvbm5lY3Rpb25JRCA9IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25JZCggJHRhc2sgKTtcblxuXHQkZmllbGRzQ29udGFpbmVyLmNsb3Nlc3QoICcud3Bmb3Jtcy1wYW5lbCcgKS50cmlnZ2VyKCAnY29ubmVjdGlvbkdlbmVyYWxTZXR0aW5nc1JlbmRlcmVkJywgWyBFTlRJVFlfU0xVRywgY29ubmVjdGlvbklEIF0gKTtcblx0aW5pdGlhbExvYWQgPSB0cnVlO1xufVxuXG4vKipcbiAqIENhbGxiYWNrIGZ1bmN0aW9uIHRyaWdnZXJlZCB3aGVuIGFjY291bnQgZGF0YSBpcyBsb2FkZWQuXG4gKiBJdCByZS1yZW5kZXJzIHRoZSBleHBvcnQtcmVsYXRlZCBvcHRpb25zIGZvciBlYWNoIGV4cG9ydCBlbGVtZW50IGZvdW5kLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICovXG5mdW5jdGlvbiBvbkFjY291bnRzTG9hZGVkKCkge1xuXHRjb25zdCAkdGFza3NFeHBvcnRPcHRpb25zID0gJCggY29yZVNlbGVjdG9ycy5leHBvcnRUbywgJGVsZW1lbnRzLiR0YXNrcyApO1xuXHRpbml0aWFsTG9hZCA9IGZhbHNlO1xuXG5cdCR0YXNrc0V4cG9ydE9wdGlvbnMuZWFjaCggKCBfLCBleHBvcnRUbyApID0+IHtcblx0XHQvLyBSZS1yZW5kZXIgZXhwb3J0LXJlbGF0ZWQgb3B0aW9ucy5cblx0XHRleHBvcnRUb1N3aXRjaE9wdGlvbnMuY2FsbCggJCggZXhwb3J0VG8gKSApO1xuXHR9ICk7XG59XG5cbi8qKlxuICogTG9hZHMgYWNjb3VudCBzY29wZXMgYnkgbWFraW5nIGFuIEFKQVggcmVxdWVzdCBhbmQgdXBkYXRlcyBjYWNoZSB3aXRoIHRoZSByZXRyaWV2ZWQgZGF0YS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcmV0dXJuIHtPYmplY3R9IFJldHVybnMgdGhlIEFKQVggcmVxdWVzdCBvYmplY3QsIG9yIHVuZGVmaW5lZCBpZiB0aGUgcmVzcG9uc2UgaXMgaW52YWxpZC5cbiAqL1xuZnVuY3Rpb24gbG9hZEFjY291bnRzU2NvcGVzKCkge1xuXHRjb25zdCBkYXRhID0ge1xuXHRcdHRhc2s6IGBkcm9wYm94X2xvYWRfYWNjb3VudHNfc2NvcGVzYCxcblx0fTtcblxuXHRyZXR1cm4gQWpheC5yZXF1ZXN0KCBFTlRJVFlfU0xVRywgeyBkYXRhIH0gKS50aGVuKCBmdW5jdGlvbiggcmVzcG9uc2UgKSB7XG5cdFx0aWYgKCAhIHJlc3BvbnNlLnN1Y2Nlc3MgfHwgdHlwZW9mIHJlc3BvbnNlLmRhdGE/LmFjY291bnRzICE9PSAnb2JqZWN0JyApIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cblx0XHRDYWNoZS5zZXQoIEVOVElUWV9TTFVHLCAnYWNjb3VudHNXaXRoU2NvcGVzJywgcmVzcG9uc2UuZGF0YS5hY2NvdW50cyApO1xuXHR9ICk7XG59XG5cbi8qKlxuICogSGFuZGxlcyB0aGUgYWRkaXRpb24gb2YgYSBuZXcgY29ubmVjdGlvbiB0cmlnZ2VyZWQgYnkgdGhlIHVzZXIuXG4gKiBUaGUgbWV0aG9kIHByZXZlbnRzIHRoZSBkZWZhdWx0IGV2ZW50IGJlaGF2aW9yLCBnZXRzIHRoZSBhc3NvY2lhdGVkIHRhc2ssXG4gKiBoaWRlcyB0aGUgXCJhZGQgY29ubmVjdGlvblwiIGJ1dHRvbiwgYW5kIHJlLXJlbmRlcnMgZXhwb3J0LXJlbGF0ZWQgb3B0aW9ucyBpbiB0aGUgVUkuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtFdmVudH0gZXZlbnQgVGhlIGV2ZW50IG9iamVjdCB0cmlnZ2VyZWQgYnkgdGhlIFwiYWRkIGNvbm5lY3Rpb25cIiBhY3Rpb24uXG4gKi9cbmZ1bmN0aW9uIG9uQWRkQ29ubmVjdGlvbiggZXZlbnQgKSB7XG5cdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cblx0Y29uc3QgJGJ1dHRvbiA9ICQoIHRoaXMgKTtcblx0Y29uc3QgJHRhc2sgPSAkYnV0dG9uLmNsb3Nlc3QoIGNvcmVTZWxlY3RvcnMudGFzayApO1xuXG5cdHNldFNob3dBZGRDb25uZWN0aW9uQnV0dG9uKCAkYnV0dG9uLCBmYWxzZSApO1xuXG5cdC8vIFJlLXJlbmRlciBleHBvcnQtcmVsYXRlZCBvcHRpb25zLlxuXHRleHBvcnRUb1N3aXRjaE9wdGlvbnMuY2FsbCggJCggY29yZVNlbGVjdG9ycy5leHBvcnRUbywgJHRhc2sgKSApO1xufVxuXG4vKipcbiAqIEhhbmRsZXMgdGhlIGV2ZW50IHRyaWdnZXJlZCB3aGVuIGEgY29ubmVjdGlvbiBpcyByZW1vdmVkLiBJdCBwcmV2ZW50cyB0aGUgZGVmYXVsdCBiZWhhdmlvcixcbiAqIGNsZWFycyB0aGUgYXNzb2NpYXRlZCBhY2NvdW50IGZpZWxkLCB1cGRhdGVzIHRoZSBidXR0b24gc3RhdGUsIGFuZCByZS1yZW5kZXJzIGV4cG9ydC1yZWxhdGVkIG9wdGlvbnMuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtFdmVudH0gZXZlbnQgVGhlIGV2ZW50IG9iamVjdCB0cmlnZ2VyZWQgYnkgdGhlIHJlbW92ZSBjb25uZWN0aW9uIGFjdGlvbi5cbiAqL1xuZnVuY3Rpb24gb25SZW1vdmVDb25uZWN0aW9uKCBldmVudCApIHtcblx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuXHRjb25zdCAkYnV0dG9uID0gJCggdGhpcyApO1xuXHRjb25zdCAkdGFzayA9ICRidXR0b24uY2xvc2VzdCggY29yZVNlbGVjdG9ycy50YXNrICk7XG5cdGNvbnN0IGNvbm5lY3Rpb25JZCA9IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25JZCggJHRhc2sgKTtcblx0Y29uc3QgeyBjb25uZWN0aW9uIH0gPSBCdWlsZGVyLmFwcC5nZXRDb25uZWN0aW9uRGF0YSggJHRhc2sgKTtcblx0Y29ubmVjdGlvbi5kcm9wYm94ID0ge307XG5cblx0Q2FjaGUuYWRkVG8oIEVOVElUWV9TTFVHLCAnY29ubmVjdGlvbnMnLCBjb25uZWN0aW9uSWQsIGNvbm5lY3Rpb24gKTtcblx0c2V0U2hvd0FkZENvbm5lY3Rpb25CdXR0b24oICRidXR0b24sIHRydWUgKTtcblxuXHQvLyBSZS1yZW5kZXIgZXhwb3J0LXJlbGF0ZWQgb3B0aW9ucy5cblx0ZXhwb3J0VG9Td2l0Y2hPcHRpb25zLmNhbGwoICQoIGNvcmVTZWxlY3RvcnMuZXhwb3J0VG8sICR0YXNrICkgKTtcbn1cblxuLyoqXG4gKiBIYW5kbGVzIHRoZSBcIkFkZCBBY2NvdW50XCIgYnV0dG9uIGNsaWNrIGV2ZW50LlxuICogUHJldmVudHMgdGhlIGRlZmF1bHQgYWN0aW9uLCBlbnN1cmVzIHRoZSBmb3JtIGlzIHNhdmVkLFxuICogYW5kIHJlZGlyZWN0cyB0byB0aGUgc3BlY2lmaWVkIFVSTC5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge0V2ZW50fSBldmVudCBUaGUgZXZlbnQgb2JqZWN0IHRyaWdnZXJlZCBieSB0aGUgYnV0dG9uIGNsaWNrLlxuICovXG5mdW5jdGlvbiBvbkFkZEFjY291bnQoIGV2ZW50ICkge1xuXHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdHNhdmVGb3JtKCk7XG59XG5cbi8qKlxuICogUmV0cmlldmVzIHRoZSByZW5kZXJpbmcgZGF0YSBmb3IgdGhlIHNwZWNpZmllZCBjb250YWluZXIsIGluY2x1ZGluZyBhY2NvdW50IGluZm9ybWF0aW9uXG4gKiBhbmQgc3RhdGUgZm9yIGRpc3BsYXlpbmcgdGhlIFwiQWRkIENvbm5lY3Rpb25cIiBidXR0b24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtIVE1MRWxlbWVudH0gJGNvbnRhaW5lciBUaGUgY29udGFpbmVyIGVsZW1lbnQgZm9yIHdoaWNoIHRoZSByZW5kZXJpbmcgZGF0YSBpcyBiZWluZyBwcmVwYXJlZC5cbiAqXG4gKiBAcmV0dXJuIHtPYmplY3R9IEFuIG9iamVjdCBjb250YWluaW5nIHRoZSByZW5kZXJpbmcgZGF0YSwgaW5jbHVkaW5nIGFjY291bnRzIGFuZCB0aGUgXCJBZGQgQ29ubmVjdGlvblwiIGJ1dHRvbiBzdGF0ZS5cbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGdldFJlbmRlckRhdGEoICRjb250YWluZXIgKSB7XG5cdGNvbnN0IGFjY291bnRzID0gQ2FjaGUuZ2V0KCBEcm9wYm94UHJvdmlkZXIucHJvdmlkZXIsICdhY2NvdW50cycgKTtcblxuXHRyZXR1cm4ge1xuXHRcdGFjY291bnRzLFxuXHRcdHNob3dBZGRDb25uZWN0aW9uQnV0dG9uOiBpc1Nob3dBZGRDb25uZWN0aW9uQnV0dG9uKCAkY29udGFpbmVyICksXG5cdH07XG59XG5cbi8qKlxuICogSGFuZGxlcyB0aGUgY2hhbmdlIGV2ZW50IGZvciB0aGUgb25EdXBsaWNhdGUgc2VsZWN0b3IuXG4gKiBJZiB0aGUgdmFsdWUgY2hhbmdlcyB0byBcImFkZFwiIGFuZCBleHBvcnRUbyBpcyBcImRyb3Bib3hcIiwgc2hvdyB0aGUgcGVybWlzc2lvbnMgbW9kYWwuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmZ1bmN0aW9uIG9uRHVwbGljYXRlQ2hhbmdlKCkge1xuXHRjb25zdCAkb25EdXBsaWNhdGUgPSAkKCB0aGlzICk7XG5cdGNvbnN0ICR0YXNrID0gJG9uRHVwbGljYXRlLmNsb3Nlc3QoIGNvcmVTZWxlY3RvcnMudGFzayApO1xuXHRjb25zdCAkZXhwb3J0VG8gPSAkKCBjb3JlU2VsZWN0b3JzLmV4cG9ydFRvLCAkdGFzayApO1xuXHRjb25zdCAkYWNjb3VudEZpZWxkID0gJCggRHJvcGJveFByb3ZpZGVyLnNlbGVjdG9ycy5hY2NvdW50RmllbGQsICR0YXNrICk7XG5cblx0aWYgKCAkb25EdXBsaWNhdGUudmFsKCkgPT09ICdhZGQnICYmICRleHBvcnRUby52YWwoKSA9PT0gJ2Ryb3Bib3gnICYmICEgaXNBY2NvdW50V2l0aFNjb3BlcyggJGV4cG9ydFRvICkgKSB7XG5cdFx0c2hvd1Blcm1pc3Npb25zTW9kYWwoICRhY2NvdW50RmllbGQgKTtcblx0fVxufVxuXG4vKipcbiAqIE5vdGlmaWVzIHRoZSB1c2VyIGFib3V0IHRoZSByZXF1aXJlZCBjb25uZWN0aW9uIGlmIHRoZSBcImV4cG9ydCB0b1wiIGZpZWxkXG4gKiBpcyBzZXQgdG8gRHJvcGJveCBhbmQgdGhlIGFjY291bnQgZmllbGQgaXMgbm90IHByZXNlbnQuXG4gKlxuICogVGhpcyBtZXRob2QgY2hlY2tzIHRoZSB0YXNrLXNwZWNpZmljIGNvbmZpZ3VyYXRpb24gYW5kIGRpc3BsYXlzIGFuIGFsZXJ0XG4gKiBpZiB0aGUgRHJvcGJveCBjb25uZWN0aW9uIGlzIHJlcXVpcmVkIGJ1dCBub3Qgc2V0IHVwLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHRhc2sgVGhlIERPTSBlbGVtZW50IHJlcHJlc2VudGluZyB0aGUgdGFzayBjb250ZXh0LlxuICovXG5leHBvcnQgZnVuY3Rpb24gbm90aWZ5QWJvdXRSZXF1aXJlZENvbm5lY3Rpb24oIHRhc2sgKSB7XG5cdGNvbnN0ICR0YXNrID0gJCggdGFzayApO1xuXHRjb25zdCAkZXhwb3J0VG8gPSAkKCBjb3JlU2VsZWN0b3JzLmV4cG9ydFRvLCAkdGFzayApO1xuXHRjb25zdCAkYWNjb3VudEZpZWxkID0gJCggRHJvcGJveFByb3ZpZGVyLnNlbGVjdG9ycy5hY2NvdW50RmllbGQsICR0YXNrICk7XG5cblx0aWYgKCAkZXhwb3J0VG8udmFsKCkgIT09ICdkcm9wYm94JyB8fCAkYWNjb3VudEZpZWxkLmxlbmd0aCB8fCBoYXNTaG93bkFsZXJ0ICkge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdGhhc1Nob3duQWxlcnQgPSB0cnVlO1xuXG5cdCQuYWxlcnQoIHtcblx0XHR0aXRsZTogd3Bmb3Jtc19idWlsZGVyLmhlYWRzX3VwLFxuXHRcdGNvbnRlbnQ6IHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5kcm9wYm94UmVxdWlyZWRDb25uZWN0aW9uLFxuXHRcdGljb246ICdmYSBmYS1leGNsYW1hdGlvbi1jaXJjbGUnLFxuXHRcdHR5cGU6ICdyZWQnLFxuXHRcdGJ1dHRvbnM6IHtcblx0XHRcdGNvbmZpcm06IHtcblx0XHRcdFx0dGV4dDogd3Bmb3Jtc19idWlsZGVyLm9rLFxuXHRcdFx0XHRidG5DbGFzczogJ2J0bi1jb25maXJtJyxcblx0XHRcdFx0a2V5czogWyAnZW50ZXInIF0sXG5cdFx0XHR9LFxuXHRcdH0sXG5cdH0gKTtcbn1cblxuLyoqXG4gKiBJbml0aWFsaXplcyB0aGUgRHJvcGJveCBpbnRlZ3JhdGlvbiBieSBzZXR0aW5nIHVwIGV2ZW50IGxpc3RlbmVyc1xuICogYW5kIGFkZGluZyBuZWNlc3NhcnkgdGVtcGxhdGVzIGZvciB0aGUgYXV0b21hdGlvbiBidWlsZGVyLlxuICpcbiAqIFRoaXMgbWV0aG9kIGJpbmRzIGEgY2hhbmdlIGV2ZW50IHRvIHRoZSBhY2NvdW50RmllbGQgc2VsZWN0b3IgYW5kXG4gKiByZWdpc3RlcnMgdGVtcGxhdGVzIHJlcXVpcmVkIGZvciBleHBvcnRpbmcgdG8gRHJvcGJveC5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGluaXREcm9wYm94KCkge1xuXHQkZWxlbWVudHMuJHRhc2tzXG5cdFx0Lm9uKCAnY2hhbmdlJywgRHJvcGJveFByb3ZpZGVyLnNlbGVjdG9ycy5hY2NvdW50RmllbGQsIGxvYWRGaWVsZHMgKVxuXHRcdC5vbiggJ2NsaWNrJywgYCR7IGNvcmVTZWxlY3RvcnMuYWRkQ29ubmVjdGlvbiB9LmRyb3Bib3hgLCBvbkFkZENvbm5lY3Rpb24gKVxuXHRcdC5vbiggJ2NsaWNrJywgYCR7IGNvcmVTZWxlY3RvcnMuYWRkQWNjb3VudCB9LmRyb3Bib3hgLCBvbkFkZEFjY291bnQgKVxuXHRcdC5vbiggJ2NsaWNrJywgc2VsZWN0b3JzLnJlbW92ZUNvbm5lY3Rpb24sIG9uUmVtb3ZlQ29ubmVjdGlvbiApXG5cdFx0Lm9uKCAnY2hhbmdlJywgY29yZVNlbGVjdG9ycy5vbkR1cGxpY2F0ZSwgb25EdXBsaWNhdGVDaGFuZ2UgKVxuXHQ7XG5cblx0RHJvcGJveFByb3ZpZGVyLiRlbGVtZW50cy4kY29ubmVjdGlvbnNcblx0XHQub24oICdjb25uZWN0aW9uc0RhdGFMb2FkZWQnLCBvbkFjY291bnRzTG9hZGVkIClcblx0O1xuXG5cdFRlbXBsYXRlcy5hZGQoIFtcblx0XHRgd3Bmb3Jtcy0keyBFTlRJVFlfU0xVRyB9LWJ1aWxkZXItY29udGVudC10YXNrLWV4cG9ydC10by1kcm9wYm94YCxcblx0XHRgd3Bmb3Jtcy0keyBFTlRJVFlfU0xVRyB9LWJ1aWxkZXItY29udGVudC10YXNrLWV4cG9ydC10by1kcm9wYm94LWZpZWxkc2AsXG5cdF0gKTtcblxuXHRjb25zdCBwcm9taXNlID0gbG9hZEFjY291bnRzU2NvcGVzKCk7XG5cblx0ZGVzdGluYXRpb25zRGF0YS5kcm9wYm94ID0gZ2V0UmVuZGVyRGF0YTtcblx0ZGVzdGluYXRpb25zSW5pdC5kcm9wYm94ID0gKCAkY29udGFpbmVyICkgPT4ge1xuXHRcdGNvbnN0ICRhY2NvdW50RmllbGQgPSAkKCBEcm9wYm94UHJvdmlkZXIuc2VsZWN0b3JzLmFjY291bnRGaWVsZCwgJGNvbnRhaW5lciApO1xuXHRcdGxvYWRGaWVsZHMuY2FsbCggJGFjY291bnRGaWVsZCApO1xuXG5cdFx0cHJvbWlzZS5maW5hbGx5KCAoKSA9PiBsb2FkRmllbGRzLmNhbGwoICRhY2NvdW50RmllbGQgKSApO1xuXHR9O1xufVxuIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7QUFFQSxJQUFBQSxVQUFBLEdBQUFDLE9BQUE7QUFDQSxJ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QUUsYUFBYTtRQUN2QkMsSUFBSSxFQUFFLENBQUUsT0FBTztNQUNoQjtJQUNEO0VBQ0QsQ0FBRSxDQUFDO0FBQ0o7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ08sU0FBU3VFLFdBQVdBLENBQUEsRUFBRztFQUM3QjVCLG9CQUFTLENBQUNDLE1BQU0sQ0FDZDRCLEVBQUUsQ0FBRSxRQUFRLEVBQUV4SCxlQUFlLENBQUNjLFNBQVMsQ0FBQ0MsWUFBWSxFQUFFb0QsVUFBVyxDQUFDLENBQ2xFcUQsRUFBRSxDQUFFLE9BQU8sS0FBQTNDLE1BQUEsQ0FBTWxFLG9CQUFhLENBQUM4RyxhQUFhLGVBQWFyQixlQUFnQixDQUFDLENBQzFFb0IsRUFBRSxDQUFFLE9BQU8sS0FBQTNDLE1BQUEsQ0FBTWxFLG9CQUFhLENBQUMrRyxVQUFVLGVBQWFiLFlBQWEsQ0FBQyxDQUNwRVcsRUFBRSxDQUFFLE9BQU8sRUFBRTFHLFNBQVMsQ0FBQzhDLGdCQUFnQixFQUFFNEMsa0JBQW1CLENBQUMsQ0FDN0RnQixFQUFFLENBQUUsUUFBUSxFQUFFN0csb0JBQWEsQ0FBQzZELFdBQVcsRUFBRXlDLGlCQUFrQixDQUFDO0VBRzlEakgsZUFBZSxDQUFDMkYsU0FBUyxDQUFDZ0MsWUFBWSxDQUNwQ0gsRUFBRSxDQUFFLHVCQUF1QixFQUFFaEMsZ0JBQWlCLENBQUM7RUFHakRiLG9CQUFTLENBQUNpRCxHQUFHLENBQUUsWUFBQS9DLE1BQUEsQ0FDRnhELHNCQUFXLHlEQUFBd0QsTUFBQSxDQUNYeEQsc0JBQVcsb0RBQ3RCLENBQUM7RUFFSCxJQUFNd0csT0FBTyxHQUFHN0Isa0JBQWtCLENBQUMsQ0FBQztFQUVwQzhCLDBCQUFnQixDQUFDbkIsT0FBTyxHQUFHRyxhQUFhO0VBQ3hDaUIsMEJBQWdCLENBQUNwQixPQUFPLEdBQUcsVUFBRUksVUFBVSxFQUFNO0lBQzVDLElBQU1sRyxhQUFhLEdBQUdmLENBQUMsQ0FBRUUsZUFBZSxDQUFDYyxTQUFTLENBQUNDLFlBQVksRUFBRWdHLFVBQVcsQ0FBQztJQUM3RTVDLFVBQVUsQ0FBQ3hGLElBQUksQ0FBRWtDLGFBQWMsQ0FBQztJQUVoQ2dILE9BQU8sQ0FBQ3JFLE9BQU8sQ0FBRTtNQUFBLE9BQU1XLFVBQVUsQ0FBQ3hGLElBQUksQ0FBRWtDLGFBQWMsQ0FBQztJQUFBLENBQUMsQ0FBQztFQUMxRCxDQUFDO0FBQ0YiLCJpZ25vcmVMaXN0IjpbXX0=
},{"./constants":4,"./createState":5,"./export.to.js":8}],7:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.initEmail = initEmail;
var _constants = require("./constants.js");
var $ = jQuery;
var selectors = {
  container: '.wpforms-smart-tags-widget-container',
  smartTagDropdown: '.wpforms-builder-dropdown-list.insert-smart-tag-dropdown .list'
};

/**
 * Filters and removes specific smart tags within the provided container,
 * excluding the smart tag with the data-value of "admin_email".
 *
 * @since 1.0.0
 */
function excludeSmartTags() {
  var $button = $(this);
  var $smartTagsList = $button.closest(selectors.container).find(selectors.smartTagDropdown);
  $smartTagsList.find('li:not([data-value="admin_email"])').remove();
}

/**
 * Initializes the email configuration by setting up the exclusions for smart tags.
 *
 * @since 1.0.0
 */
function initEmail() {
  var emailSmartTagsSelector = "".concat(_constants.selectors.exportToOptions, "[data-export-to=\"email\"] .wpforms-show-smart-tags, .mce-wpforms-smart-tags-mce-button");
  _constants.$elements.$tasks.on('click', emailSmartTagsSelector, function (event) {
    setTimeout(excludeSmartTags.bind(event.target), 0);
  });
}
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIiQiLCJqUXVlcnkiLCJzZWxlY3RvcnMiLCJjb250YWluZXIiLCJzbWFydFRhZ0Ryb3Bkb3duIiwiZXhjbHVkZVNtYXJ0VGFncyIsIiRidXR0b24iLCIkc21hcnRUYWdzTGlzdCIsImNsb3Nlc3QiLCJmaW5kIiwicmVtb3ZlIiwiaW5pdEVtYWlsIiwiZW1haWxTbWFydFRhZ3NTZWxlY3RvciIsImNvbmNhdCIsImNvcmVTZWxlY3RvcnMiLCJleHBvcnRUb09wdGlvbnMiLCIkZWxlbWVudHMiLCIkdGFza3MiLCJvbiIsImV2ZW50Iiwic2V0VGltZW91dCIsImJpbmQiLCJ0YXJnZXQiXSwic291cmNlcyI6WyJlbWFpbC5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgeyAkZWxlbWVudHMsIHNlbGVjdG9ycyBhcyBjb3JlU2VsZWN0b3JzIH0gZnJvbSAnLi9jb25zdGFudHMuanMnO1xuXG5jb25zdCAkID0galF1ZXJ5O1xuY29uc3Qgc2VsZWN0b3JzID0ge1xuXHRjb250YWluZXI6ICcud3Bmb3Jtcy1zbWFydC10YWdzLXdpZGdldC1jb250YWluZXInLFxuXHRzbWFydFRhZ0Ryb3Bkb3duOiAnLndwZm9ybXMtYnVpbGRlci1kcm9wZG93bi1saXN0Lmluc2VydC1zbWFydC10YWctZHJvcGRvd24gLmxpc3QnLFxufTtcblxuLyoqXG4gKiBGaWx0ZXJzIGFuZCByZW1vdmVzIHNwZWNpZmljIHNtYXJ0IHRhZ3Mgd2l0aGluIHRoZSBwcm92aWRlZCBjb250YWluZXIsXG4gKiBleGNsdWRpbmcgdGhlIHNtYXJ0IHRhZyB3aXRoIHRoZSBkYXRhLXZhbHVlIG9mIFwiYWRtaW5fZW1haWxcIi5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuZnVuY3Rpb24gZXhjbHVkZVNtYXJ0VGFncygpIHtcblx0Y29uc3QgJGJ1dHRvbiA9ICQoIHRoaXMgKTtcblx0Y29uc3QgJHNtYXJ0VGFnc0xpc3QgPSAkYnV0dG9uLmNsb3Nlc3QoIHNlbGVjdG9ycy5jb250YWluZXIgKS5maW5kKCBzZWxlY3RvcnMuc21hcnRUYWdEcm9wZG93biApO1xuXG5cdCRzbWFydFRhZ3NMaXN0LmZpbmQoICdsaTpub3QoW2RhdGEtdmFsdWU9XCJhZG1pbl9lbWFpbFwiXSknICkucmVtb3ZlKCk7XG59XG5cbi8qKlxuICogSW5pdGlhbGl6ZXMgdGhlIGVtYWlsIGNvbmZpZ3VyYXRpb24gYnkgc2V0dGluZyB1cCB0aGUgZXhjbHVzaW9ucyBmb3Igc21hcnQgdGFncy5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGluaXRFbWFpbCgpIHtcblx0Y29uc3QgZW1haWxTbWFydFRhZ3NTZWxlY3RvciA9IGAkeyBjb3JlU2VsZWN0b3JzLmV4cG9ydFRvT3B0aW9ucyB9W2RhdGEtZXhwb3J0LXRvPVwiZW1haWxcIl0gLndwZm9ybXMtc2hvdy1zbWFydC10YWdzLCAubWNlLXdwZm9ybXMtc21hcnQtdGFncy1tY2UtYnV0dG9uYDtcblxuXHQkZWxlbWVudHMuJHRhc2tzLm9uKCAnY2xpY2snLCBlbWFpbFNtYXJ0VGFnc1NlbGVjdG9yLCAoIGV2ZW50ICkgPT4ge1xuXHRcdHNldFRpbWVvdXQoIGV4Y2x1ZGVTbWFydFRhZ3MuYmluZCggZXZlbnQudGFyZ2V0ICksIDAgKTtcblx0fSApO1xufVxuIl0sIm1hcHBpbmdzIjoiOzs7Ozs7QUFBQSxJQUFBQSxVQUFBLEdBQUFDLE9BQUE7QUFFQSxJQUFNQyxDQUFDLEdBQUdDLE1BQU07QUFDaEIsSUFBTUMsU0FBUyxHQUFHO0VBQ2pCQyxTQUFTLEVBQUUsc0NBQXNDO0VBQ2pEQyxnQkFBZ0IsRUFBRTtBQUNuQixDQUFDOztBQUVEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLFNBQVNDLGdCQUFnQkEsQ0FBQSxFQUFHO0VBQzNCLElBQU1DLE9BQU8sR0FBR04sQ0FBQyxDQUFFLElBQUssQ0FBQztFQUN6QixJQUFNTyxjQUFjLEdBQUdELE9BQU8sQ0FBQ0UsT0FBTyxDQUFFTixTQUFTLENBQUNDLFNBQVUsQ0FBQyxDQUFDTSxJQUFJLENBQUVQLFNBQVMsQ0FBQ0UsZ0JBQWlCLENBQUM7RUFFaEdHLGNBQWMsQ0FBQ0UsSUFBSSxDQUFFLG9DQUFxQyxDQUFDLENBQUNDLE1BQU0sQ0FBQyxDQUFDO0FBQ3JFOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDTyxTQUFTQyxTQUFTQSxDQUFBLEVBQUc7RUFDM0IsSUFBTUMsc0JBQXNCLE1BQUFDLE1BQUEsQ0FBT0Msb0JBQWEsQ0FBQ0MsZUFBZSw0RkFBd0Y7RUFFeEpDLG9CQUFTLENBQUNDLE1BQU0sQ0FBQ0MsRUFBRSxDQUFFLE9BQU8sRUFBRU4sc0JBQXNCLEVBQUUsVUFBRU8sS0FBSyxFQUFNO0lBQ2xFQyxVQUFVLENBQUVmLGdCQUFnQixDQUFDZ0IsSUFBSSxDQUFFRixLQUFLLENBQUNHLE1BQU8sQ0FBQyxFQUFFLENBQUUsQ0FBQztFQUN2RCxDQUFFLENBQUM7QUFDSiIsImlnbm9yZUxpc3QiOltdfQ==
},{"./constants.js":4}],8:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.exportToSwitchOptions = exports.exportToShowEducationModal = exports.destinationsInit = exports.destinationsData = void 0;
exports.onFileFormatChange = onFileFormatChange;
var _constants = require("./constants");
var _onDuplicateOption = require("./on.duplicate.option.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); } /* global WPForms, WPFormsEducation, wpf, WPFormsBuilder */ /**
 * Export-related functions for Entry Automation.
 *
 * @since 1.0.0
 */
var $ = jQuery;

/**
 * Shared object to store callback functions for dynamic destination templates.
 * Each callback should return object with data to be passed to the template.
 *
 * @since 1.0.0
 */
var destinationsData = exports.destinationsData = {};

/**
 * Shared object to store callback functions for dynamic destination initialization.
 * Each callback should initialize the destination-specific components.
 *
 * @since 1.0.0
 */
var destinationsInit = exports.destinationsInit = {};

/**
 * Retrieves a template safely based on its name.
 * Checks if the template element exists in the DOM before retrieving it.
 * If the template does not exist, it returns false.
 *
 * @since 1.0.0
 *
 * @param {string} template The name of the template to retrieve.
 *
 * @return {boolean|Object} Returns the template object if found, otherwise false.
 */
function getTemplateSafe(template) {
  if (!$("#tmpl-".concat(template)).length) {
    return false;
  }
  return _constants.Templates.get(template);
}

/**
 * Restores the close confirmation functionality for the WPForms builder.
 *
 * @since 1.0.0
 */
function restoreCloseConfirmation() {
  WPFormsBuilder.setCloseConfirmation(true);
  $(document).off('wpformsEducationModalClose wpformsEducationSaveModalClose', restoreCloseConfirmation);
}

/**
 * Handles the display of the education modal.
 * If the selected option has the `education-modal` class and the event is defined,
 * it triggers the modal handler and prevents the default event behavior if applicable.
 *
 * @since 1.0.0
 *
 * @param {Event}  event           The event object triggering the modal.
 * @param {Object} $selectedOption The jQuery object representing the selected option.
 *
 * @return {boolean} Returns true if the modal is handled, otherwise false.
 */
function handleEducationModal(event, $selectedOption) {
  if (!$selectedOption.hasClass('education-modal') || event === undefined) {
    return false;
  }

  // We can't update the local state of the form, it will skip the form saving after refreshing the page.
  // So we need to disable the confirmation modal.
  WPFormsBuilder.setCloseConfirmation(false);
  WPFormsEducation.core.openModalButtonHandler(event, $selectedOption);

  // Remove the event listener to prevent the modal from being displayed again on click event.
  $(_constants.$elements.$tasks).off('click', _constants.selectors.exportTo, exportToShowEducationModal); // eslint-disable-line no-use-before-define

  // Restore the close confirmation functionality after the modal is closed.
  $(document).on('wpformsEducationModalClose wpformsEducationSaveModalClose', restoreCloseConfirmation);
  return !!event.preventDefault;
}

/**
 * Handles the event when the file format changes and updates the associated task element accordingly.
 *
 * @since 1.0.0
 * @since 1.2.0 Added one entry per page toggle visibility.
 */
function onFileFormatChange() {
  var $this = $(this);
  var $task = $this.closest(_constants.selectors.task);

  // Show/hide "one entry per page" toggle based on PDF format.
  $task.find(_constants.selectors.oneEntryPerPageWrap).toggle($this.val() === 'pdf');
  (0, _onDuplicateOption.onDuplicateRender)($task);
}

/**
 * Renders the template for the selected export destination.
 * This function updates the options container with the appropriate template
 * and initializes any necessary components.
 *
 * @since 1.0.0
 *
 * @param {Object} $optionsContainer The jQuery object representing the options container.
 * @param {Object} template          The template to be rendered.
 * @param {Object} $task             The task element to render the template in.
 * @param {string} exportTo          The selected export destination.
 */
function renderTemplate($optionsContainer, template, $task, exportTo) {
  var _destinationsData$exp, _destinationsData$exp2, _destinationsInit$exp;
  $optionsContainer.attr('data-export-to', exportTo);
  $optionsContainer.html(template(_objectSpread({
    connection: _constants.Builder.app.getConnectionData($task).connection,
    provider: _constants.ENTITY_SLUG
  }, (_destinationsData$exp = (_destinationsData$exp2 = destinationsData[exportTo]) === null || _destinationsData$exp2 === void 0 ? void 0 : _destinationsData$exp2.call(destinationsData, $optionsContainer)) !== null && _destinationsData$exp !== void 0 ? _destinationsData$exp : {})));
  WPForms.Admin.Builder.SmartTags.initWidgets($optionsContainer);
  wpf.initTooltips($optionsContainer);
  (_destinationsInit$exp = destinationsInit[exportTo]) === null || _destinationsInit$exp === void 0 || _destinationsInit$exp.call(destinationsInit, $optionsContainer);
}

/**
 * Handles the behavior of dynamically switching options based on the selected value.
 * This method clears the existing container, checks for a valid selection,
 * and loads a corresponding template dynamically based on the selected value.
 *
 * @since 1.0.0
 *
 * @param {Event} event The event object containing details of the change.
 */
var exportToSwitchOptions = exports.exportToSwitchOptions = function exportToSwitchOptions(event) {
  var $this = jQuery(this);
  var $selectedOption = $this.find('option:selected');
  var $task = $this.closest(_constants.selectors.task);
  var $optionsContainer = jQuery(_constants.selectors.exportToOptions, $task);
  var exportTo = $this.val();

  // Clear the container.
  $optionsContainer.html('');

  // If the selected option has the `education-modal` class,
  // we need to handle the modal and prevent the default event behavior.
  if (handleEducationModal(event, $selectedOption) || !exportTo) {
    return;
  }
  var connectionId = _constants.Builder.app.getConnectionId($task);
  $(".wpforms-entry-automation-email-note-".concat(connectionId)).toggleClass('wpforms-hidden', exportTo !== 'email');
  (0, _onDuplicateOption.onDuplicateRender)($task);
  var template = getTemplateSafe("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export-to-").concat(exportTo));
  if (!template) {
    return;
  }
  renderTemplate($optionsContainer, template, $task, exportTo);
};

/**
 * Handles the triggering of the education modal based on the selected option.
 *
 * @since 1.0.0
 *
 * @param {Event} event The event object triggered by the user interaction.
 */
var exportToShowEducationModal = exports.exportToShowEducationModal = function exportToShowEducationModal(event) {
  var $this = jQuery(this);
  var $selectedOption = $this.find('option:selected');
  if (handleEducationModal(event, $selectedOption)) {
    event.preventDefault();
  }
};
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIl9vbkR1cGxpY2F0ZU9wdGlvbiIsIl90eXBlb2YiLCJvIiwiU3ltYm9sIiwiaXRlcmF0b3IiLCJjb25zdHJ1Y3RvciIsInByb3RvdHlwZSIsIm93bktleXMiLCJlIiwiciIsInQiLCJPYmplY3QiLCJrZXlzIiwiZ2V0T3duUHJvcGVydHlTeW1ib2xzIiwiZmlsdGVyIiwiZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yIiwiZW51bWVyYWJsZSIsInB1c2giLCJhcHBseSIsIl9vYmplY3RTcHJlYWQiLCJhcmd1bWVudHMiLCJsZW5ndGgiLCJmb3JFYWNoIiwiX2RlZmluZVByb3BlcnR5IiwiZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9ycyIsImRlZmluZVByb3BlcnRpZXMiLCJkZWZpbmVQcm9wZXJ0eSIsIl90b1Byb3BlcnR5S2V5IiwidmFsdWUiLCJjb25maWd1cmFibGUiLCJ3cml0YWJsZSIsImkiLCJfdG9QcmltaXRpdmUiLCJ0b1ByaW1pdGl2ZSIsImNhbGwiLCJUeXBlRXJyb3IiLCJTdHJpbmciLCJOdW1iZXIiLCIkIiwialF1ZXJ5IiwiZGVzdGluYXRpb25zRGF0YSIsImV4cG9ydHMiLCJkZXN0aW5hdGlvbnNJbml0IiwiZ2V0VGVtcGxhdGVTYWZlIiwidGVtcGxhdGUiLCJjb25jYXQiLCJUZW1wbGF0ZXMiLCJnZXQiLCJyZXN0b3JlQ2xvc2VDb25maXJtYXRpb24iLCJXUEZvcm1zQnVpbGRlciIsInNldENsb3NlQ29uZmlybWF0aW9uIiwiZG9jdW1lbnQiLCJvZmYiLCJoYW5kbGVFZHVjYXRpb25Nb2RhbCIsImV2ZW50IiwiJHNlbGVjdGVkT3B0aW9uIiwiaGFzQ2xhc3MiLCJ1bmRlZmluZWQiLCJXUEZvcm1zRWR1Y2F0aW9uIiwiY29yZSIsIm9wZW5Nb2RhbEJ1dHRvbkhhbmRsZXIiLCIkZWxlbWVudHMiLCIkdGFza3MiLCJzZWxlY3RvcnMiLCJleHBvcnRUbyIsImV4cG9ydFRvU2hvd0VkdWNhdGlvbk1vZGFsIiwib24iLCJwcmV2ZW50RGVmYXVsdCIsIm9uRmlsZUZvcm1hdENoYW5nZSIsIiR0aGlzIiwiJHRhc2siLCJjbG9zZXN0IiwidGFzayIsImZpbmQiLCJvbmVFbnRyeVBlclBhZ2VXcmFwIiwidG9nZ2xlIiwidmFsIiwib25EdXBsaWNhdGVSZW5kZXIiLCJyZW5kZXJUZW1wbGF0ZSIsIiRvcHRpb25zQ29udGFpbmVyIiwiX2Rlc3RpbmF0aW9uc0RhdGEkZXhwIiwiX2Rlc3RpbmF0aW9uc0RhdGEkZXhwMiIsIl9kZXN0aW5hdGlvbnNJbml0JGV4cCIsImF0dHIiLCJodG1sIiwiY29ubmVjdGlvbiIsIkJ1aWxkZXIiLCJhcHAiLCJnZXRDb25uZWN0aW9uRGF0YSIsInByb3ZpZGVyIiwiRU5USVRZX1NMVUciLCJXUEZvcm1zIiwiQWRtaW4iLCJTbWFydFRhZ3MiLCJpbml0V2lkZ2V0cyIsIndwZiIsImluaXRUb29sdGlwcyIsImV4cG9ydFRvU3dpdGNoT3B0aW9ucyIsImV4cG9ydFRvT3B0aW9ucyIsImNvbm5lY3Rpb25JZCIsImdldENvbm5lY3Rpb25JZCIsInRvZ2dsZUNsYXNzIl0sInNvdXJjZXMiOlsiZXhwb3J0LnRvLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCBXUEZvcm1zLCBXUEZvcm1zRWR1Y2F0aW9uLCB3cGYsIFdQRm9ybXNCdWlsZGVyICovXG4vKipcbiAqIEV4cG9ydC1yZWxhdGVkIGZ1bmN0aW9ucyBmb3IgRW50cnkgQXV0b21hdGlvbi5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuaW1wb3J0IHsgVGVtcGxhdGVzLCBzZWxlY3RvcnMsIEVOVElUWV9TTFVHLCBCdWlsZGVyLCAkZWxlbWVudHMgfSBmcm9tICcuL2NvbnN0YW50cyc7XG5pbXBvcnQgeyBvbkR1cGxpY2F0ZVJlbmRlciB9IGZyb20gJy4vb24uZHVwbGljYXRlLm9wdGlvbi5qcyc7XG5cbmNvbnN0ICQgPSBqUXVlcnk7XG5cbi8qKlxuICogU2hhcmVkIG9iamVjdCB0byBzdG9yZSBjYWxsYmFjayBmdW5jdGlvbnMgZm9yIGR5bmFtaWMgZGVzdGluYXRpb24gdGVtcGxhdGVzLlxuICogRWFjaCBjYWxsYmFjayBzaG91bGQgcmV0dXJuIG9iamVjdCB3aXRoIGRhdGEgdG8gYmUgcGFzc2VkIHRvIHRoZSB0ZW1wbGF0ZS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuZXhwb3J0IGNvbnN0IGRlc3RpbmF0aW9uc0RhdGEgPSB7fTtcblxuLyoqXG4gKiBTaGFyZWQgb2JqZWN0IHRvIHN0b3JlIGNhbGxiYWNrIGZ1bmN0aW9ucyBmb3IgZHluYW1pYyBkZXN0aW5hdGlvbiBpbml0aWFsaXphdGlvbi5cbiAqIEVhY2ggY2FsbGJhY2sgc2hvdWxkIGluaXRpYWxpemUgdGhlIGRlc3RpbmF0aW9uLXNwZWNpZmljIGNvbXBvbmVudHMuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmV4cG9ydCBjb25zdCBkZXN0aW5hdGlvbnNJbml0ID0ge307XG5cbi8qKlxuICogUmV0cmlldmVzIGEgdGVtcGxhdGUgc2FmZWx5IGJhc2VkIG9uIGl0cyBuYW1lLlxuICogQ2hlY2tzIGlmIHRoZSB0ZW1wbGF0ZSBlbGVtZW50IGV4aXN0cyBpbiB0aGUgRE9NIGJlZm9yZSByZXRyaWV2aW5nIGl0LlxuICogSWYgdGhlIHRlbXBsYXRlIGRvZXMgbm90IGV4aXN0LCBpdCByZXR1cm5zIGZhbHNlLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSB0ZW1wbGF0ZSBUaGUgbmFtZSBvZiB0aGUgdGVtcGxhdGUgdG8gcmV0cmlldmUuXG4gKlxuICogQHJldHVybiB7Ym9vbGVhbnxPYmplY3R9IFJldHVybnMgdGhlIHRlbXBsYXRlIG9iamVjdCBpZiBmb3VuZCwgb3RoZXJ3aXNlIGZhbHNlLlxuICovXG5mdW5jdGlvbiBnZXRUZW1wbGF0ZVNhZmUoIHRlbXBsYXRlICkge1xuXHRpZiAoICEgJCggYCN0bXBsLSR7IHRlbXBsYXRlIH1gICkubGVuZ3RoICkge1xuXHRcdHJldHVybiBmYWxzZTtcblx0fVxuXG5cdHJldHVybiBUZW1wbGF0ZXMuZ2V0KCB0ZW1wbGF0ZSApO1xufVxuXG4vKipcbiAqIFJlc3RvcmVzIHRoZSBjbG9zZSBjb25maXJtYXRpb24gZnVuY3Rpb25hbGl0eSBmb3IgdGhlIFdQRm9ybXMgYnVpbGRlci5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuZnVuY3Rpb24gcmVzdG9yZUNsb3NlQ29uZmlybWF0aW9uKCkge1xuXHRXUEZvcm1zQnVpbGRlci5zZXRDbG9zZUNvbmZpcm1hdGlvbiggdHJ1ZSApO1xuXG5cdCQoIGRvY3VtZW50ICkub2ZmKCAnd3Bmb3Jtc0VkdWNhdGlvbk1vZGFsQ2xvc2Ugd3Bmb3Jtc0VkdWNhdGlvblNhdmVNb2RhbENsb3NlJywgcmVzdG9yZUNsb3NlQ29uZmlybWF0aW9uICk7XG59XG5cbi8qKlxuICogSGFuZGxlcyB0aGUgZGlzcGxheSBvZiB0aGUgZWR1Y2F0aW9uIG1vZGFsLlxuICogSWYgdGhlIHNlbGVjdGVkIG9wdGlvbiBoYXMgdGhlIGBlZHVjYXRpb24tbW9kYWxgIGNsYXNzIGFuZCB0aGUgZXZlbnQgaXMgZGVmaW5lZCxcbiAqIGl0IHRyaWdnZXJzIHRoZSBtb2RhbCBoYW5kbGVyIGFuZCBwcmV2ZW50cyB0aGUgZGVmYXVsdCBldmVudCBiZWhhdmlvciBpZiBhcHBsaWNhYmxlLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7RXZlbnR9ICBldmVudCAgICAgICAgICAgVGhlIGV2ZW50IG9iamVjdCB0cmlnZ2VyaW5nIHRoZSBtb2RhbC5cbiAqIEBwYXJhbSB7T2JqZWN0fSAkc2VsZWN0ZWRPcHRpb24gVGhlIGpRdWVyeSBvYmplY3QgcmVwcmVzZW50aW5nIHRoZSBzZWxlY3RlZCBvcHRpb24uXG4gKlxuICogQHJldHVybiB7Ym9vbGVhbn0gUmV0dXJucyB0cnVlIGlmIHRoZSBtb2RhbCBpcyBoYW5kbGVkLCBvdGhlcndpc2UgZmFsc2UuXG4gKi9cbmZ1bmN0aW9uIGhhbmRsZUVkdWNhdGlvbk1vZGFsKCBldmVudCwgJHNlbGVjdGVkT3B0aW9uICkge1xuXHRpZiAoICEgJHNlbGVjdGVkT3B0aW9uLmhhc0NsYXNzKCAnZWR1Y2F0aW9uLW1vZGFsJyApIHx8IGV2ZW50ID09PSB1bmRlZmluZWQgKSB7XG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9XG5cblx0Ly8gV2UgY2FuJ3QgdXBkYXRlIHRoZSBsb2NhbCBzdGF0ZSBvZiB0aGUgZm9ybSwgaXQgd2lsbCBza2lwIHRoZSBmb3JtIHNhdmluZyBhZnRlciByZWZyZXNoaW5nIHRoZSBwYWdlLlxuXHQvLyBTbyB3ZSBuZWVkIHRvIGRpc2FibGUgdGhlIGNvbmZpcm1hdGlvbiBtb2RhbC5cblx0V1BGb3Jtc0J1aWxkZXIuc2V0Q2xvc2VDb25maXJtYXRpb24oIGZhbHNlICk7XG5cdFdQRm9ybXNFZHVjYXRpb24uY29yZS5vcGVuTW9kYWxCdXR0b25IYW5kbGVyKCBldmVudCwgJHNlbGVjdGVkT3B0aW9uICk7XG5cblx0Ly8gUmVtb3ZlIHRoZSBldmVudCBsaXN0ZW5lciB0byBwcmV2ZW50IHRoZSBtb2RhbCBmcm9tIGJlaW5nIGRpc3BsYXllZCBhZ2FpbiBvbiBjbGljayBldmVudC5cblx0JCggJGVsZW1lbnRzLiR0YXNrcyApLm9mZiggJ2NsaWNrJywgc2VsZWN0b3JzLmV4cG9ydFRvLCBleHBvcnRUb1Nob3dFZHVjYXRpb25Nb2RhbCApOyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLXVzZS1iZWZvcmUtZGVmaW5lXG5cblx0Ly8gUmVzdG9yZSB0aGUgY2xvc2UgY29uZmlybWF0aW9uIGZ1bmN0aW9uYWxpdHkgYWZ0ZXIgdGhlIG1vZGFsIGlzIGNsb3NlZC5cblx0JCggZG9jdW1lbnQgKS5vbiggJ3dwZm9ybXNFZHVjYXRpb25Nb2RhbENsb3NlIHdwZm9ybXNFZHVjYXRpb25TYXZlTW9kYWxDbG9zZScsIHJlc3RvcmVDbG9zZUNvbmZpcm1hdGlvbiApO1xuXG5cdHJldHVybiAhISBldmVudC5wcmV2ZW50RGVmYXVsdDtcbn1cblxuLyoqXG4gKiBIYW5kbGVzIHRoZSBldmVudCB3aGVuIHRoZSBmaWxlIGZvcm1hdCBjaGFuZ2VzIGFuZCB1cGRhdGVzIHRoZSBhc3NvY2lhdGVkIHRhc2sgZWxlbWVudCBhY2NvcmRpbmdseS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqIEBzaW5jZSAxLjIuMCBBZGRlZCBvbmUgZW50cnkgcGVyIHBhZ2UgdG9nZ2xlIHZpc2liaWxpdHkuXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBvbkZpbGVGb3JtYXRDaGFuZ2UoKSB7XG5cdGNvbnN0ICR0aGlzID0gJCggdGhpcyApO1xuXHRjb25zdCAkdGFzayA9ICR0aGlzLmNsb3Nlc3QoIHNlbGVjdG9ycy50YXNrICk7XG5cblx0Ly8gU2hvdy9oaWRlIFwib25lIGVudHJ5IHBlciBwYWdlXCIgdG9nZ2xlIGJhc2VkIG9uIFBERiBmb3JtYXQuXG5cdCR0YXNrLmZpbmQoIHNlbGVjdG9ycy5vbmVFbnRyeVBlclBhZ2VXcmFwICkudG9nZ2xlKCAkdGhpcy52YWwoKSA9PT0gJ3BkZicgKTtcblxuXHRvbkR1cGxpY2F0ZVJlbmRlciggJHRhc2sgKTtcbn1cblxuLyoqXG4gKiBSZW5kZXJzIHRoZSB0ZW1wbGF0ZSBmb3IgdGhlIHNlbGVjdGVkIGV4cG9ydCBkZXN0aW5hdGlvbi5cbiAqIFRoaXMgZnVuY3Rpb24gdXBkYXRlcyB0aGUgb3B0aW9ucyBjb250YWluZXIgd2l0aCB0aGUgYXBwcm9wcmlhdGUgdGVtcGxhdGVcbiAqIGFuZCBpbml0aWFsaXplcyBhbnkgbmVjZXNzYXJ5IGNvbXBvbmVudHMuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtPYmplY3R9ICRvcHRpb25zQ29udGFpbmVyIFRoZSBqUXVlcnkgb2JqZWN0IHJlcHJlc2VudGluZyB0aGUgb3B0aW9ucyBjb250YWluZXIuXG4gKiBAcGFyYW0ge09iamVjdH0gdGVtcGxhdGUgICAgICAgICAgVGhlIHRlbXBsYXRlIHRvIGJlIHJlbmRlcmVkLlxuICogQHBhcmFtIHtPYmplY3R9ICR0YXNrICAgICAgICAgICAgIFRoZSB0YXNrIGVsZW1lbnQgdG8gcmVuZGVyIHRoZSB0ZW1wbGF0ZSBpbi5cbiAqIEBwYXJhbSB7c3RyaW5nfSBleHBvcnRUbyAgICAgICAgICBUaGUgc2VsZWN0ZWQgZXhwb3J0IGRlc3RpbmF0aW9uLlxuICovXG5mdW5jdGlvbiByZW5kZXJUZW1wbGF0ZSggJG9wdGlvbnNDb250YWluZXIsIHRlbXBsYXRlLCAkdGFzaywgZXhwb3J0VG8gKSB7XG5cdCRvcHRpb25zQ29udGFpbmVyLmF0dHIoICdkYXRhLWV4cG9ydC10bycsIGV4cG9ydFRvICk7XG5cblx0JG9wdGlvbnNDb250YWluZXIuaHRtbCggdGVtcGxhdGUoIHtcblx0XHRjb25uZWN0aW9uOiBCdWlsZGVyLmFwcC5nZXRDb25uZWN0aW9uRGF0YSggJHRhc2sgKS5jb25uZWN0aW9uLFxuXHRcdHByb3ZpZGVyOiBFTlRJVFlfU0xVRyxcblx0XHQuLi4oIGRlc3RpbmF0aW9uc0RhdGFbIGV4cG9ydFRvIF0/LiggJG9wdGlvbnNDb250YWluZXIgKSA/PyB7fSApLFxuXHR9ICkgKTtcblxuXHRXUEZvcm1zLkFkbWluLkJ1aWxkZXIuU21hcnRUYWdzLmluaXRXaWRnZXRzKCAkb3B0aW9uc0NvbnRhaW5lciApO1xuXHR3cGYuaW5pdFRvb2x0aXBzKCAkb3B0aW9uc0NvbnRhaW5lciApO1xuXHRkZXN0aW5hdGlvbnNJbml0WyBleHBvcnRUbyBdPy4oICRvcHRpb25zQ29udGFpbmVyICk7XG59XG5cbi8qKlxuICogSGFuZGxlcyB0aGUgYmVoYXZpb3Igb2YgZHluYW1pY2FsbHkgc3dpdGNoaW5nIG9wdGlvbnMgYmFzZWQgb24gdGhlIHNlbGVjdGVkIHZhbHVlLlxuICogVGhpcyBtZXRob2QgY2xlYXJzIHRoZSBleGlzdGluZyBjb250YWluZXIsIGNoZWNrcyBmb3IgYSB2YWxpZCBzZWxlY3Rpb24sXG4gKiBhbmQgbG9hZHMgYSBjb3JyZXNwb25kaW5nIHRlbXBsYXRlIGR5bmFtaWNhbGx5IGJhc2VkIG9uIHRoZSBzZWxlY3RlZCB2YWx1ZS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge0V2ZW50fSBldmVudCBUaGUgZXZlbnQgb2JqZWN0IGNvbnRhaW5pbmcgZGV0YWlscyBvZiB0aGUgY2hhbmdlLlxuICovXG5leHBvcnQgY29uc3QgZXhwb3J0VG9Td2l0Y2hPcHRpb25zID0gZnVuY3Rpb24oIGV2ZW50ICkge1xuXHRjb25zdCAkdGhpcyA9IGpRdWVyeSggdGhpcyApO1xuXHRjb25zdCAkc2VsZWN0ZWRPcHRpb24gPSAkdGhpcy5maW5kKCAnb3B0aW9uOnNlbGVjdGVkJyApO1xuXHRjb25zdCAkdGFzayA9ICR0aGlzLmNsb3Nlc3QoIHNlbGVjdG9ycy50YXNrICk7XG5cdGNvbnN0ICRvcHRpb25zQ29udGFpbmVyID0galF1ZXJ5KCBzZWxlY3RvcnMuZXhwb3J0VG9PcHRpb25zLCAkdGFzayApO1xuXHRjb25zdCBleHBvcnRUbyA9ICR0aGlzLnZhbCgpO1xuXG5cdC8vIENsZWFyIHRoZSBjb250YWluZXIuXG5cdCRvcHRpb25zQ29udGFpbmVyLmh0bWwoICcnICk7XG5cblx0Ly8gSWYgdGhlIHNlbGVjdGVkIG9wdGlvbiBoYXMgdGhlIGBlZHVjYXRpb24tbW9kYWxgIGNsYXNzLFxuXHQvLyB3ZSBuZWVkIHRvIGhhbmRsZSB0aGUgbW9kYWwgYW5kIHByZXZlbnQgdGhlIGRlZmF1bHQgZXZlbnQgYmVoYXZpb3IuXG5cdGlmICggaGFuZGxlRWR1Y2F0aW9uTW9kYWwoIGV2ZW50LCAkc2VsZWN0ZWRPcHRpb24gKSB8fCAhIGV4cG9ydFRvICkge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdGNvbnN0IGNvbm5lY3Rpb25JZCA9IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25JZCggJHRhc2sgKTtcblxuXHQkKCBgLndwZm9ybXMtZW50cnktYXV0b21hdGlvbi1lbWFpbC1ub3RlLSR7IGNvbm5lY3Rpb25JZCB9YCApLnRvZ2dsZUNsYXNzKCAnd3Bmb3Jtcy1oaWRkZW4nLCBleHBvcnRUbyAhPT0gJ2VtYWlsJyApO1xuXG5cdG9uRHVwbGljYXRlUmVuZGVyKCAkdGFzayApO1xuXG5cdGNvbnN0IHRlbXBsYXRlID0gZ2V0VGVtcGxhdGVTYWZlKFxuXHRcdGB3cGZvcm1zLSR7IEVOVElUWV9TTFVHIH0tYnVpbGRlci1jb250ZW50LXRhc2stZXhwb3J0LXRvLSR7IGV4cG9ydFRvIH1gXG5cdCk7XG5cblx0aWYgKCAhIHRlbXBsYXRlICkge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdHJlbmRlclRlbXBsYXRlKCAkb3B0aW9uc0NvbnRhaW5lciwgdGVtcGxhdGUsICR0YXNrLCBleHBvcnRUbyApO1xufTtcblxuLyoqXG4gKiBIYW5kbGVzIHRoZSB0cmlnZ2VyaW5nIG9mIHRoZSBlZHVjYXRpb24gbW9kYWwgYmFzZWQgb24gdGhlIHNlbGVjdGVkIG9wdGlvbi5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge0V2ZW50fSBldmVudCBUaGUgZXZlbnQgb2JqZWN0IHRyaWdnZXJlZCBieSB0aGUgdXNlciBpbnRlcmFjdGlvbi5cbiAqL1xuZXhwb3J0IGNvbnN0IGV4cG9ydFRvU2hvd0VkdWNhdGlvbk1vZGFsID0gZnVuY3Rpb24oIGV2ZW50ICkge1xuXHRjb25zdCAkdGhpcyA9IGpRdWVyeSggdGhpcyApO1xuXHRjb25zdCAkc2VsZWN0ZWRPcHRpb24gPSAkdGhpcy5maW5kKCAnb3B0aW9uOnNlbGVjdGVkJyApO1xuXG5cdGlmICggaGFuZGxlRWR1Y2F0aW9uTW9kYWwoIGV2ZW50LCAkc2VsZWN0ZWRPcHRpb24gKSApIHtcblx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHR9XG59O1xuIl0sIm1hcHBpbmdzIjoiOzs7Ozs7O0FBTUEsSUFBQUEsVUFBQSxHQUFBQyxPQUFBO0FBQ0EsSUFBQUMsa0JBQUEsR0FBQUQsT0FBQTtBQUE2RCxTQUFBRSxRQUFBQyxDQUFBLHNDQUFBRCxPQUFBLHdCQUFBRSxNQUFBLHVCQUFBQSxNQUFBLENBQUFDLFFBQUEsYUFBQUYsQ0FBQSxrQkFBQUEsQ0FBQSxnQkFBQUEsQ0FBQSxXQUFBQSxDQUFBLHlCQUFBQyxNQUFBLElBQUFELENBQUEsQ0FB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
},{"./constants":4,"./on.duplicate.option.js":12}],9:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.filtersRender = void 0;
var _constants = require("./constants");
var _utils = require("./utils.js");
/* global wpf, wpformsEntryAutomationBuilderVars */
/**
 * Filter-related functions for Entry Automation.
 *
 * @since 1.0.0
 */

var $ = jQuery;

/**
 * Renders the task filter options using a specified template and this data.
 *
 * @since 1.0.0
 *
 * @param {Object} $task The task element where the filter options will be rendered.
 */
var filtersRender = exports.filtersRender = function filtersRender($task) {
  // Load the specific template from Templates.
  var filterTemplate = _constants.Templates.get("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-filter"));
  var fields = wpf.getFields();

  // If the template exists, insert the generated HTML into the options container.
  $(_constants.selectors.filterOptions, $task).html(filterTemplate({
    connection: _constants.Builder.app.getConnectionData($task).connection,
    fields: fields,
    additionalFields: wpformsEntryAutomationBuilderVars.additionalFields,
    advancedFilterFields: wpformsEntryAutomationBuilderVars.advancedFilterFields,
    statuses: wpformsEntryAutomationBuilderVars.statuses,
    operators: _constants.operatorsObject,
    provider: _constants.ENTITY_SLUG
  }));

  // Init ChoicesJS.
  (0, _utils.initChoicesJS)($(_constants.selectors.filterOptions, $task), _constants.selectors.choiceJS);
};
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIl91dGlscyIsIiQiLCJqUXVlcnkiLCJmaWx0ZXJzUmVuZGVyIiwiZXhwb3J0cyIsIiR0YXNrIiwiZmlsdGVyVGVtcGxhdGUiLCJUZW1wbGF0ZXMiLCJnZXQiLCJjb25jYXQiLCJFTlRJVFlfU0xVRyIsImZpZWxkcyIsIndwZiIsImdldEZpZWxkcyIsInNlbGVjdG9ycyIsImZpbHRlck9wdGlvbnMiLCJodG1sIiwiY29ubmVjdGlvbiIsIkJ1aWxkZXIiLCJhcHAiLCJnZXRDb25uZWN0aW9uRGF0YSIsImFkZGl0aW9uYWxGaWVsZHMiLCJ3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMiLCJhZHZhbmNlZEZpbHRlckZpZWxkcyIsInN0YXR1c2VzIiwib3BlcmF0b3JzIiwib3BlcmF0b3JzT2JqZWN0IiwicHJvdmlkZXIiLCJpbml0Q2hvaWNlc0pTIiwiY2hvaWNlSlMiXSwic291cmNlcyI6WyJmaWx0ZXJzLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCB3cGYsIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycyAqL1xuLyoqXG4gKiBGaWx0ZXItcmVsYXRlZCBmdW5jdGlvbnMgZm9yIEVudHJ5IEF1dG9tYXRpb24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmltcG9ydCB7IFRlbXBsYXRlcywgc2VsZWN0b3JzLCBvcGVyYXRvcnNPYmplY3QsIEVOVElUWV9TTFVHLCBCdWlsZGVyIH0gZnJvbSAnLi9jb25zdGFudHMnO1xuaW1wb3J0IHsgaW5pdENob2ljZXNKUyB9IGZyb20gJy4vdXRpbHMuanMnO1xuXG5jb25zdCAkID0galF1ZXJ5O1xuXG4vKipcbiAqIFJlbmRlcnMgdGhlIHRhc2sgZmlsdGVyIG9wdGlvbnMgdXNpbmcgYSBzcGVjaWZpZWQgdGVtcGxhdGUgYW5kIHRoaXMgZGF0YS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gJHRhc2sgVGhlIHRhc2sgZWxlbWVudCB3aGVyZSB0aGUgZmlsdGVyIG9wdGlvbnMgd2lsbCBiZSByZW5kZXJlZC5cbiAqL1xuZXhwb3J0IGNvbnN0IGZpbHRlcnNSZW5kZXIgPSBmdW5jdGlvbiggJHRhc2sgKSB7XG5cdC8vIExvYWQgdGhlIHNwZWNpZmljIHRlbXBsYXRlIGZyb20gVGVtcGxhdGVzLlxuXHRjb25zdCBmaWx0ZXJUZW1wbGF0ZSA9IFRlbXBsYXRlcy5nZXQoIGB3cGZvcm1zLSR7IEVOVElUWV9TTFVHIH0tYnVpbGRlci1jb250ZW50LXRhc2stZmlsdGVyYCApO1xuXHRjb25zdCBmaWVsZHMgPSB3cGYuZ2V0RmllbGRzKCk7XG5cblx0Ly8gSWYgdGhlIHRlbXBsYXRlIGV4aXN0cywgaW5zZXJ0IHRoZSBnZW5lcmF0ZWQgSFRNTCBpbnRvIHRoZSBvcHRpb25zIGNvbnRhaW5lci5cblx0JCggc2VsZWN0b3JzLmZpbHRlck9wdGlvbnMsICR0YXNrICkuaHRtbCggZmlsdGVyVGVtcGxhdGUoIHtcblx0XHRjb25uZWN0aW9uOiBCdWlsZGVyLmFwcC5nZXRDb25uZWN0aW9uRGF0YSggJHRhc2sgKS5jb25uZWN0aW9uLFxuXHRcdGZpZWxkcyxcblx0XHRhZGRpdGlvbmFsRmllbGRzOiB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMuYWRkaXRpb25hbEZpZWxkcyxcblx0XHRhZHZhbmNlZEZpbHRlckZpZWxkczogd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzLmFkdmFuY2VkRmlsdGVyRmllbGRzLFxuXHRcdHN0YXR1c2VzOiB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMuc3RhdHVzZXMsXG5cdFx0b3BlcmF0b3JzOiBvcGVyYXRvcnNPYmplY3QsXG5cdFx0cHJvdmlkZXI6IEVOVElUWV9TTFVHLFxuXHR9ICkgKTtcblxuXHQvLyBJbml0IENob2ljZXNKUy5cblx0aW5pdENob2ljZXNKUyggJCggc2VsZWN0b3JzLmZpbHRlck9wdGlvbnMsICR0YXNrICksIHNlbGVjdG9ycy5jaG9pY2VKUyApO1xufTtcbiJdLCJtYXBwaW5ncyI6Ijs7Ozs7O0FBTUEsSUFBQUEsVUFBQSxHQUFBQyxPQUFBO0FBQ0EsSUFBQUMsTUFBQSxHQUFBRCxPQUFBO0FBUEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUlBLElBQU1FLENBQUMsR0FBR0MsTUFBTTs7QUFFaEI7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDTyxJQUFNQyxhQUFhLEdBQUFDLE9BQUEsQ0FBQUQsYUFBQSxHQUFHLFNBQWhCQSxhQUFhQSxDQUFhRSxLQUFLLEVBQUc7RUFDOUM7RUFDQSxJQUFNQyxjQUFjLEdBQUdDLG9CQUFTLENBQUNDLEdBQUcsWUFBQUMsTUFBQSxDQUFjQyxzQkFBVyxpQ0FBZ0MsQ0FBQztFQUM5RixJQUFNQyxNQUFNLEdBQUdDLEdBQUcsQ0FBQ0MsU0FBUyxDQUFDLENBQUM7O0VBRTlCO0VBQ0FaLENBQUMsQ0FBRWEsb0JBQVMsQ0FBQ0MsYUFBYSxFQUFFVixLQUFNLENBQUMsQ0FBQ1csSUFBSSxDQUFFVixjQUFjLENBQUU7SUFDekRXLFVBQVUsRUFBRUMsa0JBQU8sQ0FBQ0MsR0FBRyxDQUFDQyxpQkFBaUIsQ0FBRWYsS0FBTSxDQUFDLENBQUNZLFVBQVU7SUFDN0ROLE1BQU0sRUFBTkEsTUFBTTtJQUNOVSxnQkFBZ0IsRUFBRUMsaUNBQWlDLENBQUNELGdCQUFnQjtJQUNwRUUsb0JBQW9CLEVBQUVELGlDQUFpQyxDQUFDQyxvQkFBb0I7SUFDNUVDLFFBQVEsRUFBRUYsaUNBQWlDLENBQUNFLFFBQVE7SUFDcERDLFNBQVMsRUFBRUMsMEJBQWU7SUFDMUJDLFFBQVEsRUFBRWpCO0VBQ1gsQ0FBRSxDQUFFLENBQUM7O0VBRUw7RUFDQSxJQUFBa0Isb0JBQWEsRUFBRTNCLENBQUMsQ0FBRWEsb0JBQVMsQ0FBQ0MsYUFBYSxFQUFFVixLQUFNLENBQUMsRUFBRVMsb0JBQVMsQ0FBQ2UsUUFBUyxDQUFDO0FBQ3pFLENBQUMiLCJpZ25vcmVMaXN0IjpbXX0=
},{"./constants":4,"./utils.js":14}],10:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.displayAlert = displayAlert;
exports.ftpFieldsSelectors = void 0;
exports.markFields = markFields;
exports.removeFtpConnection = removeFtpConnection;
exports.testFtpConnection = testFtpConnection;
var _constants = require("./constants");
/* global wpforms_builder, wpformsEntryAutomationBuilderVars */

var $ = jQuery;
var ftpFieldsSelectors = exports.ftpFieldsSelectors = ['wpforms-entry-automation-ftp-host', 'wpforms-entry-automation-ftp-port', 'wpforms-entry-automation-ftp-username', 'wpforms-entry-automation-ftp-password', 'wpforms-entry-automation-ftp-path'];

/**
 * Get FTP connection settings.
 *
 * @since 1.0.0
 *
 * @param {string} connectionId Connection ID.
 *
 * @return {Object} FTP settings.
 */
function getFtpSettings(connectionId) {
  var settings = {};
  ftpFieldsSelectors.forEach(function (selector) {
    var $field = $("#".concat(selector, "-").concat(connectionId));
    if ($field.length) {
      var key = selector.replace('wpforms-entry-automation-ftp-', '');
      settings[key] = $field.val();
    }
  });
  return settings;
}

/**
 * Clear FTP connection fields.
 *
 * @since 1.0.0
 *
 * @param {string} connectionId Connection ID.
 */
function removeFtpConnection(connectionId) {
  ftpFieldsSelectors.forEach(function (selector) {
    var $field = $("#".concat(selector, "-").concat(connectionId));
    if ($field.length) {
      $field.val('');
    }
  });
}

/**
 * Test FTP connection.
 *
 * @since 1.0.0
 *
 * @param {string} connectionId Connection ID.
 *
 * @return {Promise} Promise.
 */
function testFtpConnection(connectionId) {
  var data = {
    connectionId: connectionId,
    task: 'test_ftp_connection',
    nonce: wpforms_builder.nonce,
    settings: getFtpSettings(connectionId)
  };
  if (!data.settings.host || !data.settings.username || !data.settings.password) {
    return Promise.reject(new Error('required_fields'));
  }
  return _constants.Ajax.request(_constants.ENTITY_SLUG, {
    data: data
  });
}

/**
 * Mark FTP fields with error class.
 *
 * @since 1.0.0
 *
 * @param {string}  connectionId Connection ID.
 * @param {boolean} isError      Is error.
 */
function markFields(connectionId, isError) {
  ftpFieldsSelectors.forEach(function (selector) {
    var $field = $("#".concat(selector, "-").concat(connectionId));
    if ($field.length && $field.hasClass('wpforms-required')) {
      $field.toggleClass('wpforms-error', isError);
    }
  });
}

/**
 * Display alert message.
 *
 * @since 1.0.0
 *
 * @param {string} connectionId Connection ID.
 * @param {string} type         Alert type (success or failed).
 */
function displayAlert(connectionId, type) {
  markFields(connectionId, type === 'failed');
  $.alert({
    title: wpformsEntryAutomationBuilderVars.ftp[type].title,
    content: wpformsEntryAutomationBuilderVars.ftp[type].content,
    icon: 'fa ' + (type !== 'success' ? 'fa-exclamation-circle' : 'fa-check-circle'),
    type: type !== 'success' ? 'red' : 'green',
    buttons: {
      confirm: {
        text: wpforms_builder.ok,
        btnClass: 'btn-confirm',
        keys: ['enter']
      }
    }
  });
}
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIiQiLCJqUXVlcnkiLCJmdHBGaWVsZHNTZWxlY3RvcnMiLCJleHBvcnRzIiwiZ2V0RnRwU2V0dGluZ3MiLCJjb25uZWN0aW9uSWQiLCJzZXR0aW5ncyIsImZvckVhY2giLCJzZWxlY3RvciIsIiRmaWVsZCIsImNvbmNhdCIsImxlbmd0aCIsImtleSIsInJlcGxhY2UiLCJ2YWwiLCJyZW1vdmVGdHBDb25uZWN0aW9uIiwidGVzdEZ0cENvbm5lY3Rpb24iLCJkYXRhIiwidGFzayIsIm5vbmNlIiwid3Bmb3Jtc19idWlsZGVyIiwiaG9zdCIsInVzZXJuYW1lIiwicGFzc3dvcmQiLCJQcm9taXNlIiwicmVqZWN0IiwiRXJyb3IiLCJBamF4IiwicmVxdWVzdCIsIkVOVElUWV9TTFVHIiwibWFya0ZpZWxkcyIsImlzRXJyb3IiLCJoYXNDbGFzcyIsInRvZ2dsZUNsYXNzIiwiZGlzcGxheUFsZXJ0IiwidHlwZSIsImFsZXJ0IiwidGl0bGUiLCJ3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMiLCJmdHAiLCJjb250ZW50IiwiaWNvbiIsImJ1dHRvbnMiLCJjb25maXJtIiwidGV4dCIsIm9rIiwiYnRuQ2xhc3MiLCJrZXlzIl0sInNvdXJjZXMiOlsiZnRwLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCB3cGZvcm1zX2J1aWxkZXIsIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycyAqL1xuXG5pbXBvcnQgeyBBamF4LCBFTlRJVFlfU0xVRyB9IGZyb20gJy4vY29uc3RhbnRzJztcblxuY29uc3QgJCA9IGpRdWVyeTtcblxuZXhwb3J0IGNvbnN0IGZ0cEZpZWxkc1NlbGVjdG9ycyA9IFtcblx0J3dwZm9ybXMtZW50cnktYXV0b21hdGlvbi1mdHAtaG9zdCcsXG5cdCd3cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tZnRwLXBvcnQnLFxuXHQnd3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLWZ0cC11c2VybmFtZScsXG5cdCd3cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tZnRwLXBhc3N3b3JkJyxcblx0J3dwZm9ybXMtZW50cnktYXV0b21hdGlvbi1mdHAtcGF0aCcsXG5dO1xuXG4vKipcbiAqIEdldCBGVFAgY29ubmVjdGlvbiBzZXR0aW5ncy5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge3N0cmluZ30gY29ubmVjdGlvbklkIENvbm5lY3Rpb24gSUQuXG4gKlxuICogQHJldHVybiB7T2JqZWN0fSBGVFAgc2V0dGluZ3MuXG4gKi9cbmZ1bmN0aW9uIGdldEZ0cFNldHRpbmdzKCBjb25uZWN0aW9uSWQgKSB7XG5cdGNvbnN0IHNldHRpbmdzID0ge307XG5cblx0ZnRwRmllbGRzU2VsZWN0b3JzLmZvckVhY2goICggc2VsZWN0b3IgKSA9PiB7XG5cdFx0Y29uc3QgJGZpZWxkID0gJCggYCMkeyBzZWxlY3RvciB9LSR7IGNvbm5lY3Rpb25JZCB9YCApO1xuXHRcdGlmICggJGZpZWxkLmxlbmd0aCApIHtcblx0XHRcdGNvbnN0IGtleSA9IHNlbGVjdG9yLnJlcGxhY2UoICd3cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tZnRwLScsICcnICk7XG5cdFx0XHRzZXR0aW5nc1sga2V5IF0gPSAkZmllbGQudmFsKCk7XG5cdFx0fVxuXHR9ICk7XG5cblx0cmV0dXJuIHNldHRpbmdzO1xufVxuXG4vKipcbiAqIENsZWFyIEZUUCBjb25uZWN0aW9uIGZpZWxkcy5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge3N0cmluZ30gY29ubmVjdGlvbklkIENvbm5lY3Rpb24gSUQuXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiByZW1vdmVGdHBDb25uZWN0aW9uKCBjb25uZWN0aW9uSWQgKSB7XG5cdGZ0cEZpZWxkc1NlbGVjdG9ycy5mb3JFYWNoKCAoIHNlbGVjdG9yICkgPT4ge1xuXHRcdGNvbnN0ICRmaWVsZCA9ICQoIGAjJHsgc2VsZWN0b3IgfS0keyBjb25uZWN0aW9uSWQgfWAgKTtcblx0XHRpZiAoICRmaWVsZC5sZW5ndGggKSB7XG5cdFx0XHQkZmllbGQudmFsKCAnJyApO1xuXHRcdH1cblx0fSApO1xufVxuXG4vKipcbiAqIFRlc3QgRlRQIGNvbm5lY3Rpb24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtzdHJpbmd9IGNvbm5lY3Rpb25JZCBDb25uZWN0aW9uIElELlxuICpcbiAqIEByZXR1cm4ge1Byb21pc2V9IFByb21pc2UuXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiB0ZXN0RnRwQ29ubmVjdGlvbiggY29ubmVjdGlvbklkICkge1xuXHRjb25zdCBkYXRhID0ge1xuXHRcdGNvbm5lY3Rpb25JZCxcblx0XHR0YXNrOiAndGVzdF9mdHBfY29ubmVjdGlvbicsXG5cdFx0bm9uY2U6IHdwZm9ybXNfYnVpbGRlci5ub25jZSxcblx0XHRzZXR0aW5nczogZ2V0RnRwU2V0dGluZ3MoIGNvbm5lY3Rpb25JZCApLFxuXHR9O1xuXG5cdGlmICggISBkYXRhLnNldHRpbmdzLmhvc3QgfHwgISBkYXRhLnNldHRpbmdzLnVzZXJuYW1lIHx8ICEgZGF0YS5zZXR0aW5ncy5wYXNzd29yZCApIHtcblx0XHRyZXR1cm4gUHJvbWlzZS5yZWplY3QoIG5ldyBFcnJvciggJ3JlcXVpcmVkX2ZpZWxkcycgKSApO1xuXHR9XG5cblx0cmV0dXJuIEFqYXgucmVxdWVzdCggRU5USVRZX1NMVUcsIHtcblx0XHRkYXRhLFxuXHR9ICk7XG59XG5cbi8qKlxuICogTWFyayBGVFAgZmllbGRzIHdpdGggZXJyb3IgY2xhc3MuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtzdHJpbmd9ICBjb25uZWN0aW9uSWQgQ29ubmVjdGlvbiBJRC5cbiAqIEBwYXJhbSB7Ym9vbGVhbn0gaXNFcnJvciAgICAgIElzIGVycm9yLlxuICovXG5leHBvcnQgZnVuY3Rpb24gbWFya0ZpZWxkcyggY29ubmVjdGlvbklkLCBpc0Vycm9yICkge1xuXHRmdHBGaWVsZHNTZWxlY3RvcnMuZm9yRWFjaCggKCBzZWxlY3RvciApID0+IHtcblx0XHRjb25zdCAkZmllbGQgPSAkKCBgIyR7IHNlbGVjdG9yIH0tJHsgY29ubmVjdGlvbklkIH1gICk7XG5cdFx0aWYgKCAkZmllbGQubGVuZ3RoICYmICRmaWVsZC5oYXNDbGFzcyggJ3dwZm9ybXMtcmVxdWlyZWQnICkgKSB7XG5cdFx0XHQkZmllbGQudG9nZ2xlQ2xhc3MoICd3cGZvcm1zLWVycm9yJywgaXNFcnJvciApO1xuXHRcdH1cblx0fSApO1xufVxuXG4vKipcbiAqIERpc3BsYXkgYWxlcnQgbWVzc2FnZS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge3N0cmluZ30gY29ubmVjdGlvbklkIENvbm5lY3Rpb24gSUQuXG4gKiBAcGFyYW0ge3N0cmluZ30gdHlwZSAgICAgICAgIEFsZXJ0IHR5cGUgKHN1Y2Nlc3Mgb3IgZmFpbGVkKS5cbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGRpc3BsYXlBbGVydCggY29ubmVjdGlvbklkLCB0eXBlICkge1xuXHRtYXJrRmllbGRzKCBjb25uZWN0aW9uSWQsIHR5cGUgPT09ICdmYWlsZWQnICk7XG5cblx0JC5hbGVydCgge1xuXHRcdHRpdGxlOiB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMuZnRwWyB0eXBlIF0udGl0bGUsXG5cdFx0Y29udGVudDogd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzLmZ0cFsgdHlwZSBdLmNvbnRlbnQsXG5cdFx0aWNvbjogJ2ZhICcgKyAoIHR5cGUgIT09ICdzdWNjZXNzJyA/ICdmYS1leGNsYW1hdGlvbi1jaXJjbGUnIDogJ2ZhLWNoZWNrLWNpcmNsZScgKSxcblx0XHR0eXBlOiB0eXBlICE9PSAnc3VjY2VzcycgPyAncmVkJyA6ICdncmVlbicsXG5cdFx0YnV0dG9uczoge1xuXHRcdFx0Y29uZmlybToge1xuXHRcdFx0XHR0ZXh0OiB3cGZvcm1zX2J1aWxkZXIub2ssXG5cdFx0XHRcdGJ0bkNsYXNzOiAnYnRuLWNvbmZpcm0nLFxuXHRcdFx0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
},{"./constants":4}],11:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.getRenderData = getRenderData;
exports.initGoogle = initGoogle;
exports.notifyAboutRequiredConnection = notifyAboutRequiredConnection;
exports.onAddConnection = onAddConnection;
exports.selectors = void 0;
var _constants = require("./constants");
var _exportTo = require("./export.to.js");
var _createState3 = require("./createState");
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; } /* global WPForms, wpforms_builder, wpformsEntryAutomationBuilderVars */
var GoogleProvider = WPForms.Admin.Builder.Providers.GoogleDrive;
var $ = jQuery;
var hasShownAlert = false;
var selectors = exports.selectors = {
  removeConnection: '.wpforms-builder-google-drive-connection-remove',
  folderOptions: '.wpforms-builder-google-drive-folder-fields'
};
var _createState = (0, _createState3.createState)('showGoogleAddConnectionButton', true),
  _createState2 = _slicedToArray(_createState, 2),
  isShowAddConnectionButton = _createState2[0],
  setShowAddConnectionButton = _createState2[1];

/**
 * Prepares and retrieves the data necessary for rendering the UI related to connections.
 *
 * @since 1.0.0
 *
 * @param {HTMLElement} $container The container element used to retrieve the connection information.
 *
 * @return {Object} An object containing render data with keys:
 * - accounts: Cached Google Provider accounts.
 * - formName: The name of the form retrieved from the settings field.
 * - showAddConnectionButton: A boolean indicating whether the "Add Connection" button should be displayed.
 */
function getRenderData($container) {
  var accounts = GoogleProvider.Cache.get(GoogleProvider.provider, 'accounts');
  return {
    accounts: accounts,
    formName: $('#wpforms-panel-field-settings-form_title').val(),
    showAddConnectionButton: isShowAddConnectionButton($container)
  };
}

/**
 * Handles the connection process for a Google account within the WPForms builder.
 * It updates the Google Drive authentication URL and processes the addition of the account through the GoogleProvider.
 *
 * @since 1.0.0
 *
 * @param {...any} props Parameters required for adding the Google account.
 */
function onConnectAccount() {
  var _GoogleProvider$accou;
  // eslint-disable-next-line camelcase
  wpforms_builder.google_drive.auth_url = wpformsEntryAutomationBuilderVars.googleDriveAuth;
  // Avoid showing alert modal.
  hasShownAlert = true;
  (_GoogleProvider$accou = GoogleProvider.account).add.apply(_GoogleProvider$accou, arguments);
}

/**
 * Temporarily changes the connection selector for GoogleProvider to perform an action in a specific scope.
 * Restores the original selector after the callback execution.
 *
 * @since 1.0.0
 *
 * @param {Function} callback The function to execute within the modified selector scope.
 */
function runInScope(callback) {
  var connectionSelector = GoogleProvider.selectors.connection,
    fieldWrapperSelector = GoogleProvider.selectors.fieldWrapper,
    folderTypeBlockSelector = GoogleProvider.selectors.folderTypeBlock;
  GoogleProvider.selectors.connection = _constants.selectors.task;
  GoogleProvider.selectors.fieldWrapper = '.wpforms-panel-field';
  GoogleProvider.selectors.folderTypeBlock = '.wpforms-builder-google-drive-provider-folder-type .wpforms-builder-entry-automation-radio-buttons';

  // Perform the action.
  callback.call(this);

  // Restore the original selectors.
  GoogleProvider.selectors.connection = connectionSelector;
  GoogleProvider.selectors.fieldWrapper = fieldWrapperSelector;
  GoogleProvider.selectors.folderTypeBlock = folderTypeBlockSelector;
}

/**
 * Handles the account selection process, toggling the visibility of account fields
 * based on the selected account and resetting related folder ID field values.
 *
 * @since 1.0.0
 */
function onChooseAccount() {
  runInScope.call(this, function () {
    // Perform the action.
    GoogleProvider.ui.accountField.change.call(this);
  });
}

/**
 * Handles the account selection process, toggling the visibility of account fields
 * based on the selected account and resetting related folder ID field values.
 *
 * @since 1.0.0
 *
 * @param {Event} event The event object triggered by the change in folder type.
 */
function onChangeFolderType(event) {
  runInScope.call(this, function () {
    // Perform the action.
    GoogleProvider.ui.folderTypeField.changeType.call(this);
  });
  var $task = event.target.closest(_constants.selectors.taskRoot);
  var connectionId = $($task).data('connection_id');
  $("#wpforms-google-drive-connection-remove-".concat(connectionId)).toggleClass('wpforms-hidden', event.target.value === 'existing');
}

/**
 * Handles the opening of a picker by temporarily altering the connection selector,
 * performing the picker action, and then restoring the original connection selector.
 *
 * @since 1.0.0
 */
function onOpenPicker() {
  var $button = $(this);

  // Opening the picker starts with an access token request, so it isn't instant.
  if ($button.prop('disabled')) {
    return;
  }
  $button.prop('disabled', true);

  // Anything else finishing in the meantime, the Heartbeat included, must not release the button.
  $(document).on('ajaxComplete.wpformsEntryAutomationFolderPicker', function (event, jqXHR, settings) {
    var _settings$data;
    if (!String((_settings$data = settings.data) !== null && _settings$data !== void 0 ? _settings$data : '').includes('access_token_get')) {
      return;
    }
    $(document).off('ajaxComplete.wpformsEntryAutomationFolderPicker');
    $button.prop('disabled', false);
  });
  runInScope.call(this, function () {
    // Perform the action.
    GoogleProvider.ui.folderIdField.openPicker.call(this);
  });
}

/**
 * Handles the folder selection process by temporarily changing the connection selector,
 * performing an action with the updated selector, and then restoring the original selector.
 *
 * This method allows for a specific state change in the UI during folder selection.
 *
 * @since 1.0.0
 */
function onPickFolder() {
  runInScope.call(this, function () {
    // Perform the action.
    GoogleProvider.ui.folderIdField.changeState.call(this);
  });
}

/**
 * Updates the folder link by temporarily modifying the GoogleProvider selector and invoking the fileLink update functionality.
 * Restores the original selector after performing the action.
 *
 * @since 1.0.0
 */
function updateFolderLink() {
  runInScope.call(this, function () {
    // Perform the action.
    GoogleProvider.ui.fileLink.update.call(this);
  });
}

/**
 * Clears the folder ID field within the GoogleProvider UI scope.
 * This method ensures that the folder ID is reset to an empty or default state.
 *
 * @since 1.0.0
 */
function clearFolderId() {
  runInScope.call(this, function () {
    // Perform the action.
    GoogleProvider.ui.folderIdField.clear.call(this);
  });
}

/**
 * Notifies the user about the required connection if the "export to" field
 * is set to Google and the account field is not present.
 *
 * This method checks the task-specific configuration and displays an alert
 * if the Google Drive connection is required but not set up.
 *
 * @since 1.0.0
 *
 * @param {HTMLElement} task The DOM element representing the task context.
 */
function notifyAboutRequiredConnection(task) {
  var $task = $(task);
  var $exportTo = $(_constants.selectors.exportTo, $task);
  var $accountField = $(GoogleProvider.selectors.accountField, $task);
  if ($exportTo.val() !== 'google' || $accountField.length || hasShownAlert) {
    return;
  }
  hasShownAlert = true;
  $.alert({
    title: wpforms_builder.heads_up,
    content: wpformsEntryAutomationBuilderVars.googleDriveRequiredConnection,
    icon: 'fa fa-exclamation-circle',
    type: 'red',
    buttons: {
      confirm: {
        text: wpforms_builder.ok,
        btnClass: 'btn-confirm',
        keys: ['enter']
      }
    }
  });
}

/**
 * Handles the "Add Connection" event triggered by user interaction.
 * Prevents the default form submission, updates the state of the add connection button,
 * and re-renders export-related options for a specific task element.
 *
 * @since 1.0.0
 *
 * @param {Event} event The event object triggered during the operation.
 */
function onAddConnection(event) {
  event.preventDefault();
  var $button = $(this);
  var $task = $button.closest(_constants.selectors.task);
  setShowAddConnectionButton($button, false);

  // Re-render export-related options.
  _exportTo.exportToSwitchOptions.call($(_constants.selectors.exportTo, $task));
}

/**
 * Handles the event triggered when a connection is removed. It prevents the default behavior,
 * clears the associated account field, updates the button state, and re-renders export-related options.
 *
 * @since 1.0.0
 *
 * @param {Event} event The event object triggered by the remove connection action.
 */
function onRemoveConnection(event) {
  event.preventDefault();
  var $button = $(this);
  var $task = $button.closest(_constants.selectors.task);
  var connectionId = _constants.Builder.app.getConnectionId($task);
  var _Builder$app$getConne = _constants.Builder.app.getConnectionData($task),
    connection = _Builder$app$getConne.connection;
  connection.google = {};
  _constants.Cache.addTo(_constants.ENTITY_SLUG, 'connections', connectionId, connection);
  setShowAddConnectionButton($button, true);

  // Re-render export-related options.
  _exportTo.exportToSwitchOptions.call($(_constants.selectors.exportTo, $task));
}
var initialLoad = false;

/**
 * Renders folder options into the specified container element.
 *
 * This method clears the existing folder options in the provided container
 * and populates it by rendering a template with the necessary data.
 *
 * @since 1.0.0
 *
 * @param {Object} $container The jQuery object representing the container element where folder options will be rendered.
 */
function renderFolderOptions($container) {
  var $folderContainer = $(selectors.folderOptions, $container);
  $folderContainer.html('');

  // Load the specific template from Templates.
  var template = _constants.Templates.get("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export-to-google-folder"));
  $folderContainer.html(template({
    connection: _constants.Builder.app.getConnectionData($container).connection,
    provider: _constants.ENTITY_SLUG,
    formName: $('#wpforms-panel-field-settings-form_title').val(),
    canSelectExistingFolder: wpformsEntryAutomationBuilderVars.canSelectExistingFolder
  }));
  updateFolderLink.call($(GoogleProvider.selectors.folderIdField, $container));
  if (initialLoad) {
    return;
  }
  var connectionID = _constants.Builder.app.getConnectionId($folderContainer);
  $folderContainer.closest('.wpforms-panel').trigger('connectionGeneralSettingsRendered', [_constants.ENTITY_SLUG, connectionID]);
  initialLoad = true;
}
var runOnce = false;

/**
 * Callback function triggered when account data is loaded.
 * It re-renders the export-related options for each export element found.
 *
 * @since 1.0.0
 */
function onAccountsLoaded() {
  // We should do it only when the GoogleProvider.connection.dataLoad method was successfully executed.
  // We should do nothing after the GoogleProvider.connection.refresh method was executed.
  if (runOnce) {
    return;
  }
  runOnce = true;
  initialLoad = false;
  var $tasksExportOptions = $(_constants.selectors.exportTo, _constants.$elements.$tasks);
  $tasksExportOptions.each(function (_, exportTo) {
    // Re-render export-related options.
    _exportTo.exportToSwitchOptions.call($(exportTo));
  });
}

/**
 * Binds Google-specific events to corresponding DOM elements and handlers.
 * The function listens for user interactions and triggers account-related operations via GoogleProvider.
 *
 * @since 1.0.0
 */
function initGoogle() {
  _constants.$elements.$tasks.on('click', GoogleProvider.selectors.authButton, onConnectAccount).on('change', GoogleProvider.selectors.accountField, onChooseAccount).on('click', "".concat(_constants.selectors.addConnection, ".google"), onAddConnection).on('click', selectors.removeConnection, onRemoveConnection).on('change', GoogleProvider.selectors.folderTypeField, onChangeFolderType).on('click', GoogleProvider.selectors.folderIdFieldChoose, onOpenPicker).on('input', GoogleProvider.selectors.folderIdField, onPickFolder).on('input', GoogleProvider.selectors.folderIdField, updateFolderLink).on('click', GoogleProvider.selectors.folderRemoveBtn, clearFolderId);
  GoogleProvider.$elements.$connections.on('connectionsDataLoaded', onAccountsLoaded);
  _constants.Templates.add(["wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export-to-google"), "wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-export-to-google-folder")]);
  _exportTo.destinationsData.google = getRenderData;
  _exportTo.destinationsInit.google = renderFolderOptions;
}
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIl9leHBvcnRUbyIsIl9jcmVhdGVTdGF0ZTMiLCJfc2xpY2VkVG9BcnJheSIsInIiLCJlIiwiX2FycmF5V2l0aEhvbGVzIiwiX2l0ZXJhYmxlVG9BcnJheUxpbWl0IiwiX3Vuc3VwcG9ydGVkSXRlcmFibGVUb0FycmF5IiwiX25vbkl0ZXJhYmxlUmVzdCIsIlR5cGVFcnJvciIsImEiLCJfYXJyYXlMaWtlVG9BcnJheSIsInQiLCJ0b1N0cmluZyIsImNhbGwiLCJzbGljZSIsImNvbnN0cnVjdG9yIiwibmFtZSIsIkFycmF5IiwiZnJvbSIsInRlc3QiLCJsZW5ndGgiLCJuIiwibCIsIlN5bWJvbCIsIml0ZXJhdG9yIiwiaSIsInUiLCJmIiwibyIsIm5leHQiLCJPYmplY3QiLCJkb25lIiwicHVzaCIsInZhbHVlIiwicmV0dXJuIiwiaXNBcnJheSIsIkdvb2dsZVByb3ZpZGVyIiwiV1BGb3JtcyIsIkFkbWluIiwiQnVpbGRlciIsIlByb3ZpZGVycyIsIkdvb2dsZURyaXZlIiwiJCIsImpRdWVyeSIsImhhc1Nob3duQWxlcnQiLCJzZWxlY3RvcnMiLCJleHBvcnRzIiwicmVtb3ZlQ29ubmVjdGlvbiIsImZvbGRlck9wdGlvbnMiLCJfY3JlYXRlU3RhdGUiLCJjcmVhdGVTdGF0ZSIsIl9jcmVhdGVTdGF0ZTIiLCJpc1Nob3dBZGRDb25uZWN0aW9uQnV0dG9uIiwic2V0U2hvd0FkZENvbm5lY3Rpb25CdXR0b24iLCJnZXRSZW5kZXJEYXRhIiwiJGNvbnRhaW5lciIsImFjY291bnRzIiwiQ2FjaGUiLCJnZXQiLCJwcm92aWRlciIsImZvcm1OYW1lIiwidmFsIiwic2hvd0FkZENvbm5lY3Rpb25CdXR0b24iLCJvbkNvbm5lY3RBY2NvdW50IiwiX0dvb2dsZVByb3ZpZGVyJGFjY291Iiwid3Bmb3Jtc19idWlsZGVyIiwiZ29vZ2xlX2RyaXZlIiwiYXV0aF91cmwiLCJ3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMiLCJnb29nbGVEcml2ZUF1dGgiLCJhY2NvdW50IiwiYWRkIiwiYXBwbHkiLCJhcmd1bWVudHMiLCJydW5JblNjb3BlIiwiY2FsbGJhY2siLCJjb25uZWN0aW9uU2VsZWN0b3IiLCJjb25uZWN0aW9uIiwiZmllbGRXcmFwcGVyU2VsZWN0b3IiLCJmaWVsZFdyYXBwZXIiLCJmb2xkZXJUeXBlQmxvY2tTZWxlY3RvciIsImZvbGRlclR5cGVCbG9jayIsImNvcmVTZWxlY3RvcnMiLCJ0YXNrIiwib25DaG9vc2VBY2NvdW50IiwidWkiLCJhY2NvdW50RmllbGQiLCJjaGFuZ2UiLCJvbkNoYW5nZUZvbGRlclR5cGUiLCJldmVudCIsImZvbGRlclR5cGVGaWVsZCIsImNoYW5nZVR5cGUiLCIkdGFzayIsInRhcmdldCIsImNsb3Nlc3QiLCJ0YXNrUm9vdCIsImNvbm5lY3Rpb25JZCIsImRhdGEiLCJjb25jYXQiLCJ0b2dnbGVDbGFzcyIsIm9uT3BlblBpY2tlciIsIiRidXR0b24iLCJwcm9wIiwiZG9jdW1lbnQiLCJvbiIsImpxWEhSIiwic2V0dGluZ3MiLCJfc2V0dGluZ3MkZGF0YSIsIlN0cmluZyIsImluY2x1ZGVzIiwib2ZmIiwiZm9sZGVySWRGaWVsZCIsIm9wZW5QaWNrZXIiLCJvblBpY2tGb2xkZXIiLCJjaGFuZ2VTdGF0ZSIsInVwZGF0ZUZvbGRlckxpbmsiLCJmaWxlTGluayIsInVwZGF0ZSIsImNsZWFyRm9sZGVySWQiLCJjbGVhciIsIm5vdGlmeUFib3V0UmVxdWlyZWRDb25uZWN0aW9uIiwiJGV4cG9ydFRvIiwiZXhwb3J0VG8iLCIkYWNjb3VudEZpZWxkIiwiYWxlcnQiLCJ0aXRsZSIsImhlYWRzX3VwIiwiY29udGVudCIsImdvb2dsZURyaXZlUmVxdWlyZWRDb25uZWN0aW9uIiwiaWNvbiIsInR5cGUiLCJidXR0b25zIiwiY29uZmlybSIsInRleHQiLCJvayIsImJ0bkNsYXNzIiwia2V5cyIsIm9uQWRkQ29ubmVjdGlvbiIsInByZXZlbnREZWZhdWx0IiwiZXhwb3J0VG9Td2l0Y2hPcHRpb25zIiwib25SZW1vdmVDb25uZWN0aW9uIiwiYXBwIiwiZ2V0Q29ubmVjdGlvbklkIiwiX0J1aWxkZXIkYXBwJGdldENvbm5lIiwiZ2V0Q29ubmVjdGlvbkRhdGEiLCJnb29nbGUiLCJhZGRUbyIsIkVOVElUWV9TTFVHIiwiaW5pdGlhbExvYWQiLCJyZW5kZXJGb2xkZXJPcHRpb25zIiwiJGZvbGRlckNvbnRhaW5lciIsImh0bWwiLCJ0ZW1wbGF0ZSIsIlRlbXBsYXRlcyIsImNhblNlbGVjdEV4aXN0aW5nRm9sZGVyIiwiY29ubmVjdGlvbklEIiwidHJpZ2dlciIsInJ1bk9uY2UiLCJvbkFjY291bnRzTG9hZGVkIiwiJHRhc2tzRXhwb3J0T3B0aW9ucyIsIiRlbGVtZW50cyIsIiR0YXNrcyIsImVhY2giLCJfIiwiaW5pdEdvb2dsZSIsImF1dGhCdXR0b24iLCJhZGRDb25uZWN0aW9uIiwiZm9sZGVySWRGaWVsZENob29zZSIsImZvbGRlclJlbW92ZUJ0biIsIiRjb25uZWN0aW9ucyIsImRlc3RpbmF0aW9uc0RhdGEiLCJkZXN0aW5hdGlvbnNJbml0Il0sInNvdXJjZXMiOlsiZ29vZ2xlLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCBXUEZvcm1zLCB3cGZvcm1zX2J1aWxkZXIsIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycyAqL1xuXG5pbXBvcnQgeyAkZWxlbWVudHMsIEVOVElUWV9TTFVHLCBzZWxlY3RvcnMgYXMgY29yZVNlbGVjdG9ycywgQ2FjaGUsIFRlbXBsYXRlcywgQnVpbGRlciB9IGZyb20gJy4vY29uc3RhbnRzJztcbmltcG9ydCB7IGV4cG9ydFRvU3dpdGNoT3B0aW9ucywgZGVzdGluYXRpb25zRGF0YSwgZGVzdGluYXRpb25zSW5pdCB9IGZyb20gJy4vZXhwb3J0LnRvLmpzJztcbmltcG9ydCB7IGNyZWF0ZVN0YXRlIH0gZnJvbSAnLi9jcmVhdGVTdGF0ZSc7XG5cbmNvbnN0IEdvb2dsZVByb3ZpZGVyID0gV1BGb3Jtcy5BZG1pbi5CdWlsZGVyLlByb3ZpZGVycy5Hb29nbGVEcml2ZTtcbmNvbnN0ICQgPSBqUXVlcnk7XG5sZXQgaGFzU2hvd25BbGVydCA9IGZhbHNlO1xuXG5leHBvcnQgY29uc3Qgc2VsZWN0b3JzID0ge1xuXHRyZW1vdmVDb25uZWN0aW9uOiAnLndwZm9ybXMtYnVpbGRlci1nb29nbGUtZHJpdmUtY29ubmVjdGlvbi1yZW1vdmUnLFxuXHRmb2xkZXJPcHRpb25zOiAnLndwZm9ybXMtYnVpbGRlci1nb29nbGUtZHJpdmUtZm9sZGVyLWZpZWxkcycsXG59O1xuXG5jb25zdCBbIGlzU2hvd0FkZENvbm5lY3Rpb25CdXR0b24sIHNldFNob3dBZGRDb25uZWN0aW9uQnV0dG9uIF0gPSBjcmVhdGVTdGF0ZSggJ3Nob3dHb29nbGVBZGRDb25uZWN0aW9uQnV0dG9uJywgdHJ1ZSApO1xuXG4vKipcbiAqIFByZXBhcmVzIGFuZCByZXRyaWV2ZXMgdGhlIGRhdGEgbmVjZXNzYXJ5IGZvciByZW5kZXJpbmcgdGhlIFVJIHJlbGF0ZWQgdG8gY29ubmVjdGlvbnMuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtIVE1MRWxlbWVudH0gJGNvbnRhaW5lciBUaGUgY29udGFpbmVyIGVsZW1lbnQgdXNlZCB0byByZXRyaWV2ZSB0aGUgY29ubmVjdGlvbiBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAcmV0dXJuIHtPYmplY3R9IEFuIG9iamVjdCBjb250YWluaW5nIHJlbmRlciBkYXRhIHdpdGgga2V5czpcbiAqIC0gYWNjb3VudHM6IENhY2hlZCBHb29nbGUgUHJvdmlkZXIgYWNjb3VudHMuXG4gKiAtIGZvcm1OYW1lOiBUaGUgbmFtZSBvZiB0aGUgZm9ybSByZXRyaWV2ZWQgZnJvbSB0aGUgc2V0dGluZ3MgZmllbGQuXG4gKiAtIHNob3dBZGRDb25uZWN0aW9uQnV0dG9uOiBBIGJvb2xlYW4gaW5kaWNhdGluZyB3aGV0aGVyIHRoZSBcIkFkZCBDb25uZWN0aW9uXCIgYnV0dG9uIHNob3VsZCBiZSBkaXNwbGF5ZWQuXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBnZXRSZW5kZXJEYXRhKCAkY29udGFpbmVyICkge1xuXHRjb25zdCBhY2NvdW50cyA9IEdvb2dsZVByb3ZpZGVyLkNhY2hlLmdldCggR29vZ2xlUHJvdmlkZXIucHJvdmlkZXIsICdhY2NvdW50cycgKTtcblxuXHRyZXR1cm4ge1xuXHRcdGFjY291bnRzLFxuXHRcdGZvcm1OYW1lOiAkKCAnI3dwZm9ybXMtcGFuZWwtZmllbGQtc2V0dGluZ3MtZm9ybV90aXRsZScgKS52YWwoKSxcblx0XHRzaG93QWRkQ29ubmVjdGlvbkJ1dHRvbjogaXNTaG93QWRkQ29ubmVjdGlvbkJ1dHRvbiggJGNvbnRhaW5lciApLFxuXHR9O1xufVxuXG4vKipcbiAqIEhhbmRsZXMgdGhlIGNvbm5lY3Rpb24gcHJvY2VzcyBmb3IgYSBHb29nbGUgYWNjb3VudCB3aXRoaW4gdGhlIFdQRm9ybXMgYnVpbGRlci5cbiAqIEl0IHVwZGF0ZXMgdGhlIEdvb2dsZSBEcml2ZSBhdXRoZW50aWNhdGlvbiBVUkwgYW5kIHByb2Nlc3NlcyB0aGUgYWRkaXRpb24gb2YgdGhlIGFjY291bnQgdGhyb3VnaCB0aGUgR29vZ2xlUHJvdmlkZXIuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHsuLi5hbnl9IHByb3BzIFBhcmFtZXRlcnMgcmVxdWlyZWQgZm9yIGFkZGluZyB0aGUgR29vZ2xlIGFjY291bnQuXG4gKi9cbmZ1bmN0aW9uIG9uQ29ubmVjdEFjY291bnQoIC4uLnByb3BzICkge1xuXHQvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgY2FtZWxjYXNlXG5cdHdwZm9ybXNfYnVpbGRlci5nb29nbGVfZHJpdmUuYXV0aF91cmwgPSB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMuZ29vZ2xlRHJpdmVBdXRoO1xuXHQvLyBBdm9pZCBzaG93aW5nIGFsZXJ0IG1vZGFsLlxuXHRoYXNTaG93bkFsZXJ0ID0gdHJ1ZTtcblxuXHRHb29nbGVQcm92aWRlci5hY2NvdW50LmFkZCggLi4ucHJvcHMgKTtcbn1cblxuLyoqXG4gKiBUZW1wb3JhcmlseSBjaGFuZ2VzIHRoZSBjb25uZWN0aW9uIHNlbGVjdG9yIGZvciBHb29nbGVQcm92aWRlciB0byBwZXJmb3JtIGFuIGFjdGlvbiBpbiBhIHNwZWNpZmljIHNjb3BlLlxuICogUmVzdG9yZXMgdGhlIG9yaWdpbmFsIHNlbGVjdG9yIGFmdGVyIHRoZSBjYWxsYmFjayBleGVjdXRpb24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2sgVGhlIGZ1bmN0aW9uIHRvIGV4ZWN1dGUgd2l0aGluIHRoZSBtb2RpZmllZCBzZWxlY3RvciBzY29wZS5cbiAqL1xuZnVuY3Rpb24gcnVuSW5TY29wZSggY2FsbGJhY2sgKSB7XG5cdGNvbnN0IGNvbm5lY3Rpb25TZWxlY3RvciA9IEdvb2dsZVByb3ZpZGVyLnNlbGVjdG9ycy5jb25uZWN0aW9uLFxuXHRcdGZpZWxkV3JhcHBlclNlbGVjdG9yID0gR29vZ2xlUHJvdmlkZXIuc2VsZWN0b3JzLmZpZWxkV3JhcHBlcixcblx0XHRmb2xkZXJUeXBlQmxvY2tTZWxlY3RvciA9IEdvb2dsZVByb3ZpZGVyLnNlbGVjdG9ycy5mb2xkZXJUeXBlQmxvY2s7XG5cblx0R29vZ2xlUHJvdmlkZXIuc2VsZWN0b3JzLmNvbm5lY3Rpb24gPSBjb3JlU2VsZWN0b3JzLnRhc2s7XG5cdEdvb2dsZVByb3ZpZGVyLnNlbGVjdG9ycy5maWVsZFdyYXBwZXIgPSAnLndwZm9ybXMtcGFuZWwtZmllbGQnO1xuXHRHb29nbGVQcm92aWRlci5zZWxlY3RvcnMuZm9sZGVyVHlwZUJsb2NrID0gJy53cGZvcm1zLWJ1aWxkZXItZ29vZ2xlLWRyaXZlLXByb3ZpZGVyLWZvbGRlci10eXBlIC53cGZvcm1zLWJ1aWxkZXItZW50cnktYXV0b21hdGlvbi1yYWRpby1idXR0b25zJztcblxuXHQvLyBQZXJmb3JtIHRoZSBhY3Rpb24uXG5cdGNhbGxiYWNrLmNhbGwoIHRoaXMgKTtcblxuXHQvLyBSZXN0b3JlIHRoZSBvcmlnaW5hbCBzZWxlY3RvcnMuXG5cdEdvb2dsZVByb3ZpZGVyLnNlbGVjdG9ycy5jb25uZWN0aW9uID0gY29ubmVjdGlvblNlbGVjdG9yO1xuXHRHb29nbGVQcm92aWRlci5zZWxlY3RvcnMuZmllbGRXcmFwcGVyID0gZmllbGRXcmFwcGVyU2VsZWN0b3I7XG5cdEdvb2dsZVByb3ZpZGVyLnNlbGVjdG9ycy5mb2xkZXJUeXBlQmxvY2sgPSBmb2xkZXJUeXBlQmxvY2tTZWxlY3Rvcjtcbn1cblxuLyoqXG4gKiBIYW5kbGVzIHRoZSBhY2NvdW50IHNlbGVjdGlvbiBwcm9jZXNzLCB0b2dnbGluZyB0aGUgdmlzaWJpbGl0eSBvZiBhY2NvdW50IGZpZWxkc1xuICogYmFzZWQgb24gdGhlIHNlbGVjdGVkIGFjY291bnQgYW5kIHJlc2V0dGluZyByZWxhdGVkIGZvbGRlciBJRCBmaWVsZCB2YWx1ZXMuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmZ1bmN0aW9uIG9uQ2hvb3NlQWNjb3VudCgpIHtcblx0cnVuSW5TY29wZS5jYWxsKCB0aGlzLCBmdW5jdGlvbigpIHtcblx0XHQvLyBQZXJmb3JtIHRoZSBhY3Rpb24uXG5cdFx0R29vZ2xlUHJvdmlkZXIudWkuYWNjb3VudEZpZWxkLmNoYW5nZS5jYWxsKCB0aGlzICk7XG5cdH0gKTtcbn1cblxuLyoqXG4gKiBIYW5kbGVzIHRoZSBhY2NvdW50IHNlbGVjdGlvbiBwcm9jZXNzLCB0b2dnbGluZyB0aGUgdmlzaWJpbGl0eSBvZiBhY2NvdW50IGZpZWxkc1xuICogYmFzZWQgb24gdGhlIHNlbGVjdGVkIGFjY291bnQgYW5kIHJlc2V0dGluZyByZWxhdGVkIGZvbGRlciBJRCBmaWVsZCB2YWx1ZXMuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtFdmVudH0gZXZlbnQgVGhlIGV2ZW50IG9iamVjdCB0cmlnZ2VyZWQgYnkgdGhlIGNoYW5nZSBpbiBmb2xkZXIgdHlwZS5cbiAqL1xuZnVuY3Rpb24gb25DaGFuZ2VGb2xkZXJUeXBlKCBldmVudCApIHtcblx0cnVuSW5TY29wZS5jYWxsKCB0aGlzLCBmdW5jdGlvbigpIHtcblx0XHQvLyBQZXJmb3JtIHRoZSBhY3Rpb24uXG5cdFx0R29vZ2xlUHJvdmlkZXIudWkuZm9sZGVyVHlwZUZpZWxkLmNoYW5nZVR5cGUuY2FsbCggdGhpcyApO1xuXHR9ICk7XG5cblx0Y29uc3QgJHRhc2sgPSBldmVudC50YXJnZXQuY2xvc2VzdCggY29yZVNlbGVjdG9ycy50YXNrUm9vdCApO1xuXHRjb25zdCBjb25uZWN0aW9uSWQgPSAkKCAkdGFzayApLmRhdGEoICdjb25uZWN0aW9uX2lkJyApO1xuXG5cdCQoIGAjd3Bmb3Jtcy1nb29nbGUtZHJpdmUtY29ubmVjdGlvbi1yZW1vdmUtJHsgY29ubmVjdGlvbklkIH1gICkudG9nZ2xlQ2xhc3MoICd3cGZvcm1zLWhpZGRlbicsIGV2ZW50LnRhcmdldC52YWx1ZSA9PT0gJ2V4aXN0aW5nJyApO1xufVxuXG4vKipcbiAqIEhhbmRsZXMgdGhlIG9wZW5pbmcgb2YgYSBwaWNrZXIgYnkgdGVtcG9yYXJpbHkgYWx0ZXJpbmcgdGhlIGNvbm5lY3Rpb24gc2VsZWN0b3IsXG4gKiBwZXJmb3JtaW5nIHRoZSBwaWNrZXIgYWN0aW9uLCBhbmQgdGhlbiByZXN0b3JpbmcgdGhlIG9yaWdpbmFsIGNvbm5lY3Rpb24gc2VsZWN0b3IuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmZ1bmN0aW9uIG9uT3BlblBpY2tlcigpIHtcblx0Y29uc3QgJGJ1dHRvbiA9ICQoIHRoaXMgKTtcblxuXHQvLyBPcGVuaW5nIHRoZSBwaWNrZXIgc3RhcnRzIHdpdGggYW4gYWNjZXNzIHRva2VuIHJlcXVlc3QsIHNvIGl0IGlzbid0IGluc3RhbnQuXG5cdGlmICggJGJ1dHRvbi5wcm9wKCAnZGlzYWJsZWQnICkgKSB7XG5cdFx0cmV0dXJuO1xuXHR9XG5cblx0JGJ1dHRvbi5wcm9wKCAnZGlzYWJsZWQnLCB0cnVlICk7XG5cblx0Ly8gQW55dGhpbmcgZWxzZSBmaW5pc2hpbmcgaW4gdGhlIG1lYW50aW1lLCB0aGUgSGVhcnRiZWF0IGluY2x1ZGVkLCBtdXN0IG5vdCByZWxlYXNlIHRoZSBidXR0b24uXG5cdCQoIGRvY3VtZW50ICkub24oICdhamF4Q29tcGxldGUud3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkZvbGRlclBpY2tlcicsICggZXZlbnQsIGpxWEhSLCBzZXR0aW5ncyApID0+IHtcblx0XHRpZiAoICEgU3RyaW5nKCBzZXR0aW5ncy5kYXRhID8/ICcnICkuaW5jbHVkZXMoICdhY2Nlc3NfdG9rZW5fZ2V0JyApICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdCQoIGRvY3VtZW50ICkub2ZmKCAnYWpheENvbXBsZXRlLndwZm9ybXNFbnRyeUF1dG9tYXRpb25Gb2xkZXJQaWNrZXInICk7XG5cdFx0JGJ1dHRvbi5wcm9wKCAnZGlzYWJsZWQnLCBmYWxzZSApO1xuXHR9ICk7XG5cblx0cnVuSW5TY29wZS5jYWxsKCB0aGlzLCBmdW5jdGlvbigpIHtcblx0XHQvLyBQZXJmb3JtIHRoZSBhY3Rpb24uXG5cdFx0R29vZ2xlUHJvdmlkZXIudWkuZm9sZGVySWRGaWVsZC5vcGVuUGlja2VyLmNhbGwoIHRoaXMgKTtcblx0fSApO1xufVxuXG4vKipcbiAqIEhhbmRsZXMgdGhlIGZvbGRlciBzZWxlY3Rpb24gcHJvY2VzcyBieSB0ZW1wb3JhcmlseSBjaGFuZ2luZyB0aGUgY29ubmVjdGlvbiBzZWxlY3RvcixcbiAqIHBlcmZvcm1pbmcgYW4gYWN0aW9uIHdpdGggdGhlIHVwZGF0ZWQgc2VsZWN0b3IsIGFuZCB0aGVuIHJlc3RvcmluZyB0aGUgb3JpZ2luYWwgc2VsZWN0b3IuXG4gKlxuICogVGhpcyBtZXRob2QgYWxsb3dzIGZvciBhIHNwZWNpZmljIHN0YXRlIGNoYW5nZSBpbiB0aGUgVUkgZHVyaW5nIGZvbGRlciBzZWxlY3Rpb24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmZ1bmN0aW9uIG9uUGlja0ZvbGRlcigpIHtcblx0cnVuSW5TY29wZS5jYWxsKCB0aGlzLCBmdW5jdGlvbigpIHtcblx0XHQvLyBQZXJmb3JtIHRoZSBhY3Rpb24uXG5cdFx0R29vZ2xlUHJvdmlkZXIudWkuZm9sZGVySWRGaWVsZC5jaGFuZ2VTdGF0ZS5jYWxsKCB0aGlzICk7XG5cdH0gKTtcbn1cblxuLyoqXG4gKiBVcGRhdGVzIHRoZSBmb2xkZXIgbGluayBieSB0ZW1wb3JhcmlseSBtb2RpZnlpbmcgdGhlIEdvb2dsZVByb3ZpZGVyIHNlbGVjdG9yIGFuZCBpbnZva2luZyB0aGUgZmlsZUxpbmsgdXBkYXRlIGZ1bmN0aW9uYWxpdHkuXG4gKiBSZXN0b3JlcyB0aGUgb3JpZ2luYWwgc2VsZWN0b3IgYWZ0ZXIgcGVyZm9ybWluZyB0aGUgYWN0aW9uLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICovXG5mdW5jdGlvbiB1cGRhdGVGb2xkZXJMaW5rKCkge1xuXHRydW5JblNjb3BlLmNhbGwoIHRoaXMsIGZ1bmN0aW9uKCkge1xuXHRcdC8vIFBlcmZvcm0gdGhlIGFjdGlvbi5cblx0XHRHb29nbGVQcm92aWRlci51aS5maWxlTGluay51cGRhdGUuY2FsbCggdGhpcyApO1xuXHR9ICk7XG59XG5cbi8qKlxuICogQ2xlYXJzIHRoZSBmb2xkZXIgSUQgZmllbGQgd2l0aGluIHRoZSBHb29nbGVQcm92aWRlciBVSSBzY29wZS5cbiAqIFRoaXMgbWV0aG9kIGVuc3VyZXMgdGhhdCB0aGUgZm9sZGVyIElEIGlzIHJlc2V0IHRvIGFuIGVtcHR5IG9yIGRlZmF1bHQgc3RhdGUuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmZ1bmN0aW9uIGNsZWFyRm9sZGVySWQoKSB7XG5cdHJ1bkluU2NvcGUuY2FsbCggdGhpcywgZnVuY3Rpb24oKSB7XG5cdFx0Ly8gUGVyZm9ybSB0aGUgYWN0aW9uLlxuXHRcdEdvb2dsZVByb3ZpZGVyLnVpLmZvbGRlcklkRmllbGQuY2xlYXIuY2FsbCggdGhpcyApO1xuXHR9ICk7XG59XG5cbi8qKlxuICogTm90aWZpZXMgdGhlIHVzZXIgYWJvdXQgdGhlIHJlcXVpcmVkIGNvbm5lY3Rpb24gaWYgdGhlIFwiZXhwb3J0IHRvXCIgZmllbGRcbiAqIGlzIHNldCB0byBHb29nbGUgYW5kIHRoZSBhY2NvdW50IGZpZWxkIGlzIG5vdCBwcmVzZW50LlxuICpcbiAqIFRoaXMgbWV0aG9kIGNoZWNrcyB0aGUgdGFzay1zcGVjaWZpYyBjb25maWd1cmF0aW9uIGFuZCBkaXNwbGF5cyBhbiBhbGVydFxuICogaWYgdGhlIEdvb2dsZSBEcml2ZSBjb25uZWN0aW9uIGlzIHJlcXVpcmVkIGJ1dCBub3Qgc2V0IHVwLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHRhc2sgVGhlIERPTSBlbGVtZW50IHJlcHJlc2VudGluZyB0aGUgdGFzayBjb250ZXh0LlxuICovXG5leHBvcnQgZnVuY3Rpb24gbm90aWZ5QWJvdXRSZXF1aXJlZENvbm5lY3Rpb24oIHRhc2sgKSB7XG5cdGNvbnN0ICR0YXNrID0gJCggdGFzayApO1xuXHRjb25zdCAkZXhwb3J0VG8gPSAkKCBjb3JlU2VsZWN0b3JzLmV4cG9ydFRvLCAkdGFzayApO1xuXHRjb25zdCAkYWNjb3VudEZpZWxkID0gJCggR29vZ2xlUHJvdmlkZXIuc2VsZWN0b3JzLmFjY291bnRGaWVsZCwgJHRhc2sgKTtcblxuXHRpZiAoICRleHBvcnRUby52YWwoKSAhPT0gJ2dvb2dsZScgfHwgJGFjY291bnRGaWVsZC5sZW5ndGggfHwgaGFzU2hvd25BbGVydCApIHtcblx0XHRyZXR1cm47XG5cdH1cblxuXHRoYXNTaG93bkFsZXJ0ID0gdHJ1ZTtcblxuXHQkLmFsZXJ0KCB7XG5cdFx0dGl0bGU6IHdwZm9ybXNfYnVpbGRlci5oZWFkc191cCxcblx0XHRjb250ZW50OiB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMuZ29vZ2xlRHJpdmVSZXF1aXJlZENvbm5lY3Rpb24sXG5cdFx0aWNvbjogJ2ZhIGZhLWV4Y2xhbWF0aW9uLWNpcmNsZScsXG5cdFx0dHlwZTogJ3JlZCcsXG5cdFx0YnV0dG9uczoge1xuXHRcdFx0Y29uZmlybToge1xuXHRcdFx0XHR0ZXh0OiB3cGZvcm1zX2J1aWxkZXIub2ssXG5cdFx0XHRcdGJ0bkNsYXNzOiAnYnRuLWNvbmZpcm0nLFxuXHRcdFx0XHRrZXlzOiBbICdlbnRlcicgXSxcblx0XHRcdH0sXG5cdFx0fSxcblx0fSApO1xufVxuXG4vKipcbiAqIEhhbmRsZXMgdGhlIFwiQWRkIENvbm5lY3Rpb25cIiBldmVudCB0cmlnZ2VyZWQgYnkgdXNlciBpbnRlcmFjdGlvbi5cbiAqIFByZXZlbnRzIHRoZSBkZWZhdWx0IGZvcm0gc3VibWlzc2lvbiwgdXBkYXRlcyB0aGUgc3RhdGUgb2YgdGhlIGFkZCBjb25uZWN0aW9uIGJ1dHRvbixcbiAqIGFuZCByZS1yZW5kZXJzIGV4cG9ydC1yZWxhdGVkIG9wdGlvbnMgZm9yIGEgc3BlY2lmaWMgdGFzayBlbGVtZW50LlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50IFRoZSBldmVudCBvYmplY3QgdHJpZ2dlcmVkIGR1cmluZyB0aGUgb3BlcmF0aW9uLlxuICovXG5leHBvcnQgZnVuY3Rpb24gb25BZGRDb25uZWN0aW9uKCBldmVudCApIHtcblx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuXHRjb25zdCAkYnV0dG9uID0gJCggdGhpcyApO1xuXHRjb25zdCAkdGFzayA9ICRidXR0b24uY2xvc2VzdCggY29yZVNlbGVjdG9ycy50YXNrICk7XG5cblx0c2V0U2hvd0FkZENvbm5lY3Rpb25CdXR0b24oICRidXR0b24sIGZhbHNlICk7XG5cblx0Ly8gUmUtcmVuZGVyIGV4cG9ydC1yZWxhdGVkIG9wdGlvbnMuXG5cdGV4cG9ydFRvU3dpdGNoT3B0aW9ucy5jYWxsKCAkKCBjb3JlU2VsZWN0b3JzLmV4cG9ydFRvLCAkdGFzayApICk7XG59XG5cbi8qKlxuICogSGFuZGxlcyB0aGUgZXZlbnQgdHJpZ2dlcmVkIHdoZW4gYSBjb25uZWN0aW9uIGlzIHJlbW92ZWQuIEl0IHByZXZlbnRzIHRoZSBkZWZhdWx0IGJlaGF2aW9yLFxuICogY2xlYXJzIHRoZSBhc3NvY2lhdGVkIGFjY291bnQgZmllbGQsIHVwZGF0ZXMgdGhlIGJ1dHRvbiBzdGF0ZSwgYW5kIHJlLXJlbmRlcnMgZXhwb3J0LXJlbGF0ZWQgb3B0aW9ucy5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge0V2ZW50fSBldmVudCBUaGUgZXZlbnQgb2JqZWN0IHRyaWdnZXJlZCBieSB0aGUgcmVtb3ZlIGNvbm5lY3Rpb24gYWN0aW9uLlxuICovXG5mdW5jdGlvbiBvblJlbW92ZUNvbm5lY3Rpb24oIGV2ZW50ICkge1xuXHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdGNvbnN0ICRidXR0b24gPSAkKCB0aGlzICk7XG5cdGNvbnN0ICR0YXNrID0gJGJ1dHRvbi5jbG9zZXN0KCBjb3JlU2VsZWN0b3JzLnRhc2sgKTtcblx0Y29uc3QgY29ubmVjdGlvbklkID0gQnVpbGRlci5hcHAuZ2V0Q29ubmVjdGlvbklkKCAkdGFzayApO1xuXHRjb25zdCB7IGNvbm5lY3Rpb24gfSA9IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25EYXRhKCAkdGFzayApO1xuXHRjb25uZWN0aW9uLmdvb2dsZSA9IHt9O1xuXG5cdENhY2hlLmFkZFRvKCBFTlRJVFlfU0xVRywgJ2Nvbm5lY3Rpb25zJywgY29ubmVjdGlvbklkLCBjb25uZWN0aW9uICk7XG5cdHNldFNob3dBZGRDb25uZWN0aW9uQnV0dG9uKCAkYnV0dG9uLCB0cnVlICk7XG5cblx0Ly8gUmUtcmVuZGVyIGV4cG9ydC1yZWxhdGVkIG9wdGlvbnMuXG5cdGV4cG9ydFRvU3dpdGNoT3B0aW9ucy5jYWxsKCAkKCBjb3JlU2VsZWN0b3JzLmV4cG9ydFRvLCAkdGFzayApICk7XG59XG5cbmxldCBpbml0aWFsTG9hZCA9IGZhbHNlO1xuXG4vKipcbiAqIFJlbmRlcnMgZm9sZGVyIG9wdGlvbnMgaW50byB0aGUgc3BlY2lmaWVkIGNvbnRhaW5lciBlbGVtZW50LlxuICpcbiAqIFRoaXMgbWV0aG9kIGNsZWFycyB0aGUgZXhpc3RpbmcgZm9sZGVyIG9wdGlvbnMgaW4gdGhlIHByb3ZpZGVkIGNvbnRhaW5lclxuICogYW5kIHBvcHVsYXRlcyBpdCBieSByZW5kZXJpbmcgYSB0ZW1wbGF0ZSB3aXRoIHRoZSBuZWNlc3NhcnkgZGF0YS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gJGNvbnRhaW5lciBUaGUgalF1ZXJ5IG9iamVjdCByZXByZXNlbnRpbmcgdGhlIGNvbnRhaW5lciBlbGVtZW50IHdoZXJlIGZvbGRlciBvcHRpb25zIHdpbGwgYmUgcmVuZGVyZWQuXG4gKi9cbmZ1bmN0aW9uIHJlbmRlckZvbGRlck9wdGlvbnMoICRjb250YWluZXIgKSB7XG5cdGNvbnN0ICRmb2xkZXJDb250YWluZXIgPSAkKCBzZWxlY3RvcnMuZm9sZGVyT3B0aW9ucywgJGNvbnRhaW5lciApO1xuXHQkZm9sZGVyQ29udGFpbmVyLmh0bWwoICcnICk7XG5cblx0Ly8gTG9hZCB0aGUgc3BlY2lmaWMgdGVtcGxhdGUgZnJvbSBUZW1wbGF0ZXMuXG5cdGNvbnN0IHRlbXBsYXRlID0gVGVtcGxhdGVzLmdldChcblx0XHRgd3Bmb3Jtcy0keyBFTlRJVFlfU0xVRyB9LWJ1aWxkZXItY29udGVudC10YXNrLWV4cG9ydC10by1nb29nbGUtZm9sZGVyYFxuXHQpO1xuXG5cdCRmb2xkZXJDb250YWluZXIuaHRtbCggdGVtcGxhdGUoIHtcblx0XHRjb25uZWN0aW9uOiBCdWlsZGVyLmFwcC5nZXRDb25uZWN0aW9uRGF0YSggJGNvbnRhaW5lciApLmNvbm5lY3Rpb24sXG5cdFx0cHJvdmlkZXI6IEVOVElUWV9TTFVHLFxuXHRcdGZvcm1OYW1lOiAkKCAnI3dwZm9ybXMtcGFuZWwtZmllbGQtc2V0dGluZ3MtZm9ybV90aXRsZScgKS52YWwoKSxcblx0XHRjYW5TZWxlY3RFeGlzdGluZ0ZvbGRlcjogd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzLmNhblNlbGVjdEV4aXN0aW5nRm9sZGVyLFxuXHR9ICkgKTtcblxuXHR1cGRhdGVGb2xkZXJMaW5rLmNhbGwoICQoIEdvb2dsZVByb3ZpZGVyLnNlbGVjdG9ycy5mb2xkZXJJZEZpZWxkLCAkY29udGFpbmVyICkgKTtcblxuXHRpZiAoIGluaXRpYWxMb2FkICkge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdGNvbnN0IGNvbm5lY3Rpb25JRCA9IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25JZCggJGZvbGRlckNvbnRhaW5lciApO1xuXG5cdCRmb2xkZXJDb250YWluZXIuY2xvc2VzdCggJy53cGZvcm1zLXBhbmVsJyApLnRyaWdnZXIoICdjb25uZWN0aW9uR2VuZXJhbFNldHRpbmdzUmVuZGVyZWQnLCBbIEVOVElUWV9TTFVHLCBjb25uZWN0aW9uSUQgXSApO1xuXHRpbml0aWFsTG9hZCA9IHRydWU7XG59XG5cbmxldCBydW5PbmNlID0gZmFsc2U7XG5cbi8qKlxuICogQ2FsbGJhY2sgZnVuY3Rpb24gdHJpZ2dlcmVkIHdoZW4gYWNjb3VudCBkYXRhIGlzIGxvYWRlZC5cbiAqIEl0IHJlLXJlbmRlcnMgdGhlIGV4cG9ydC1yZWxhdGVkIG9wdGlvbnMgZm9yIGVhY2ggZXhwb3J0IGVsZW1lbnQgZm91bmQuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cbmZ1bmN0aW9uIG9uQWNjb3VudHNMb2FkZWQoKSB7XG5cdC8vIFdlIHNob3VsZCBkbyBpdCBvbmx5IHdoZW4gdGhlIEdvb2dsZVByb3ZpZGVyLmNvbm5lY3Rpb24uZGF0YUxvYWQgbWV0aG9kIHdhcyBzdWNjZXNzZnVsbHkgZXhlY3V0ZWQuXG5cdC8vIFdlIHNob3VsZCBkbyBub3RoaW5nIGFmdGVyIHRoZSBHb29nbGVQcm92aWRlci5jb25uZWN0aW9uLnJlZnJlc2ggbWV0aG9kIHdhcyBleGVjdXRlZC5cblx0aWYgKCBydW5PbmNlICkge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdHJ1bk9uY2UgPSB0cnVlO1xuXHRpbml0aWFsTG9hZCA9IGZhbHNlO1xuXHRjb25zdCAkdGFza3NFeHBvcnRPcHRpb25zID0gJCggY29yZVNlbGVjdG9ycy5leHBvcnRUbywgJGVsZW1lbnRzLiR0YXNrcyApO1xuXG5cdCR0YXNrc0V4cG9ydE9wdGlvbnMuZWFjaCggKCBfLCBleHBvcnRUbyApID0+IHtcblx0XHQvLyBSZS1yZW5kZXIgZXhwb3J0LXJlbGF0ZWQgb3B0aW9ucy5cblx0XHRleHBvcnRUb1N3aXRjaE9wdGlvbnMuY2FsbCggJCggZXhwb3J0VG8gKSApO1xuXHR9ICk7XG59XG5cbi8qKlxuICogQmluZHMgR29vZ2xlLXNwZWNpZmljIGV2ZW50cyB0byBjb3JyZXNwb25kaW5nIERPTSBlbGVtZW50cyBhbmQgaGFuZGxlcnMuXG4gKiBUaGUgZnVuY3Rpb24gbGlzdGVucyBmb3IgdXNlciBpbnRlcmFjdGlvbnMgYW5kIHRyaWdnZXJzIGFjY291bnQtcmVsYXRlZCBvcGVyYXRpb25zIHZpYSBHb29nbGVQcm92aWRlci5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGluaXRHb29nbGUoKSB7XG5cdCRlbGVtZW50cy4kdGFza3Ncblx0XHQub24oICdjbGljaycsIEdvb2dsZVByb3ZpZGVyLnNlbGVjdG9ycy5hdXRoQnV0dG9uLCBvbkNvbm5lY3RBY2NvdW50IClcblx0XHQub24oICdjaGFuZ2UnLCBHb29nbGVQcm92aWRlci5zZWxlY3RvcnMuYWNjb3VudEZpZWxkLCBvbkNob29zZUFjY291bnQgKVxuXHRcdC5vbiggJ2NsaWNrJywgYCR7IGNvcmVTZWxlY3RvcnMuYWRkQ29ubmVjdGlvbiB9Lmdvb2dsZWAsIG9uQWRkQ29ubmVjdGlvbiApXG5cdFx0Lm9uKCAnY2xpY2snLCBzZWxlY3RvcnMucmVtb3ZlQ29ubmVjdGlvbiwgb25SZW1vdmVDb25uZWN0aW9uIClcblx0XHQub24oICdjaGFuZ2UnLCBHb29nbGVQcm92aWRlci5zZWxlY3RvcnMuZm9sZGVyVHlwZUZpZWxkLCBvbkNoYW5nZUZvbGRlclR5cGUgKVxuXHRcdC5vbiggJ2NsaWNrJywgR29vZ2xlUHJvdmlkZXIuc2VsZWN0b3JzLmZvbGRlcklkRmllbGRDaG9vc2UsIG9uT3BlblBpY2tlciApXG5cdFx0Lm9uKCAnaW5wdXQnLCBHb29nbGVQcm92aWRlci5zZWxlY3RvcnMuZm9sZGVySWRGaWVsZCwgb25QaWNrRm9sZGVyIClcblx0XHQub24oICdpbnB1dCcsIEdvb2dsZVByb3ZpZGVyLnNlbGVjdG9ycy5mb2xkZXJJZEZpZWxkLCB1cGRhdGVGb2xkZXJMaW5rIClcblx0XHQub24oICdjbGljaycsIEdvb2dsZVByb3ZpZGVyLnNlbGVjdG9ycy5mb2xkZXJSZW1vdmVCdG4sIGNsZWFyRm9sZGVySWQgKVxuXHQ7XG5cblx0R29vZ2xlUHJvdmlkZXIuJGVsZW1lbnRzLiRjb25uZWN0aW9uc1xuXHRcdC5vbiggJ2Nvbm5lY3Rpb25zRGF0YUxvYWRlZCcsIG9uQWNjb3VudHNMb2FkZWQgKTtcblxuXHRUZW1wbGF0ZXMuYWRkKCBbXG5cdFx0YHdwZm9ybXMtJHsgRU5USVRZX1NMVUcgfS1idWlsZGVyLWNvbnRlbnQtdGFzay1leHBvcnQtdG8tZ29vZ2xlYCxcblx0XHRgd3Bmb3Jtcy0keyBFTlRJVFlfU0xVRyB9LWJ1aWxkZXItY29udGVudC10YXNrLWV4cG9ydC10by1nb29nbGUtZm9sZGVyYCxcblx0XSApO1xuXG5cdGRlc3RpbmF0aW9uc0RhdGEuZ29vZ2xlID0gZ2V0UmVuZGVyRGF0YTtcblx0ZGVzdGluYXRpb25zSW5pdC5nb29nbGUgPSByZW5kZXJGb2xkZXJPcHRpb25zO1xufVxuIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7O0FBRUEsSUFBQUEsVUFBQSxHQUFBQyxPQUFBO0FBQ0EsSUFBQUMsU0FBQSxHQUFBRCxPQUFBO0FBQ0EsSUFBQUUsYUFBQSxHQUFBRixPQUFBO0FBQTRDLFNBQUFHLGVBQUFDLENBQUEsRUFBQUMsQ0FBQSxXQUFBQyxlQUFBLENBQUFGLENBQUEsS0FBQUcscUJBQUEsQ0FBQUgsQ0FBQSxFQUFBQyxDQUFBLEtBQUFHLDJCQUFBLENBQUFKLENBQUEsRUFBQUMsQ0FBQSxLQUFBSSxnQkFBQTtBQUFBLFNBQUFBLGlCQUFBLGNBQUFD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
},{"./constants":4,"./createState":5,"./export.to.js":8}],12:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.onDuplicateRender = onDuplicateRender;
var _constants = require("./constants");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); } /* global wpformsEntryAutomationBuilderVars */
var $ = jQuery;

/**
 * Renders the "If File Already Exist" option for a given task in the builder interface.
 *
 * @since 1.0.0
 *
 * @param {Object} $task The jQuery object representing the task container.
 */
function onDuplicateRender($task) {
  var _connection$on_duplic;
  var template = _constants.Templates.get("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-on-duplicate-option"));
  var $container = $task.find(_constants.selectors.onDuplicateWrap);
  var exportTo = $(_constants.selectors.exportTo, $task).val();
  var fileFormat = $(_constants.selectors.fileFormat, $task).val();
  var onDuplicateOption = $(_constants.selectors.onDuplicate, $task);
  var connection = _constants.Builder.app.getConnectionData($task).connection;
  var currentValue = (_connection$on_duplic = connection.on_duplicate) !== null && _connection$on_duplic !== void 0 ? _connection$on_duplic : 'inc';
  if (onDuplicateOption.length) {
    currentValue = onDuplicateOption.val();
  }
  $container.html(template({
    connection: connection,
    onDuplicateOptions: _objectSpread({}, wpformsEntryAutomationBuilderVars.onDuplicateOptions),
    provider: _constants.ENTITY_SLUG,
    exportTo: exportTo,
    fileFormat: fileFormat,
    currentValue: currentValue
  }));
}
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIl90eXBlb2YiLCJvIiwiU3ltYm9sIiwiaXRlcmF0b3IiLCJjb25zdHJ1Y3RvciIsInByb3RvdHlwZSIsIm93bktleXMiLCJlIiwiciIsInQiLCJPYmplY3QiLCJrZXlzIiwiZ2V0T3duUHJvcGVydHlTeW1ib2xzIiwiZmlsdGVyIiwiZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yIiwiZW51bWVyYWJsZSIsInB1c2giLCJhcHBseSIsIl9vYmplY3RTcHJlYWQiLCJhcmd1bWVudHMiLCJsZW5ndGgiLCJmb3JFYWNoIiwiX2RlZmluZVByb3BlcnR5IiwiZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9ycyIsImRlZmluZVByb3BlcnRpZXMiLCJkZWZpbmVQcm9wZXJ0eSIsIl90b1Byb3BlcnR5S2V5IiwidmFsdWUiLCJjb25maWd1cmFibGUiLCJ3cml0YWJsZSIsImkiLCJfdG9QcmltaXRpdmUiLCJ0b1ByaW1pdGl2ZSIsImNhbGwiLCJUeXBlRXJyb3IiLCJTdHJpbmciLCJOdW1iZXIiLCIkIiwialF1ZXJ5Iiwib25EdXBsaWNhdGVSZW5kZXIiLCIkdGFzayIsIl9jb25uZWN0aW9uJG9uX2R1cGxpYyIsInRlbXBsYXRlIiwiVGVtcGxhdGVzIiwiZ2V0IiwiY29uY2F0IiwiRU5USVRZX1NMVUciLCIkY29udGFpbmVyIiwiZmluZCIsInNlbGVjdG9ycyIsIm9uRHVwbGljYXRlV3JhcCIsImV4cG9ydFRvIiwidmFsIiwiZmlsZUZvcm1hdCIsIm9uRHVwbGljYXRlT3B0aW9uIiwib25EdXBsaWNhdGUiLCJjb25uZWN0aW9uIiwiQnVpbGRlciIsImFwcCIsImdldENvbm5lY3Rpb25EYXRhIiwiY3VycmVudFZhbHVlIiwib25fZHVwbGljYXRlIiwiaHRtbCIsIm9uRHVwbGljYXRlT3B0aW9ucyIsIndwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycyIsInByb3ZpZGVyIl0sInNvdXJjZXMiOlsib24uZHVwbGljYXRlLm9wdGlvbi5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKiBnbG9iYWwgd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzICovXG5cbmltcG9ydCB7IHNlbGVjdG9ycywgVGVtcGxhdGVzLCBFTlRJVFlfU0xVRywgQnVpbGRlciB9IGZyb20gJy4vY29uc3RhbnRzJztcblxuY29uc3QgJCA9IGpRdWVyeTtcblxuLyoqXG4gKiBSZW5kZXJzIHRoZSBcIklmIEZpbGUgQWxyZWFkeSBFeGlzdFwiIG9wdGlvbiBmb3IgYSBnaXZlbiB0YXNrIGluIHRoZSBidWlsZGVyIGludGVyZmFjZS5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gJHRhc2sgVGhlIGpRdWVyeSBvYmplY3QgcmVwcmVzZW50aW5nIHRoZSB0YXNrIGNvbnRhaW5lci5cbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIG9uRHVwbGljYXRlUmVuZGVyKCAkdGFzayApIHtcblx0Y29uc3QgdGVtcGxhdGUgPSBUZW1wbGF0ZXMuZ2V0KCBgd3Bmb3Jtcy0keyBFTlRJVFlfU0xVRyB9LWJ1aWxkZXItY29udGVudC10YXNrLW9uLWR1cGxpY2F0ZS1vcHRpb25gICk7XG5cdGNvbnN0ICRjb250YWluZXIgPSAkdGFzay5maW5kKCBzZWxlY3RvcnMub25EdXBsaWNhdGVXcmFwICk7XG5cblx0Y29uc3QgZXhwb3J0VG8gPSAkKCBzZWxlY3RvcnMuZXhwb3J0VG8sICR0YXNrICkudmFsKCk7XG5cdGNvbnN0IGZpbGVGb3JtYXQgPSAkKCBzZWxlY3RvcnMuZmlsZUZvcm1hdCwgJHRhc2sgKS52YWwoKTtcblx0Y29uc3Qgb25EdXBsaWNhdGVPcHRpb24gPSAkKCBzZWxlY3RvcnMub25EdXBsaWNhdGUsICR0YXNrICk7XG5cdGNvbnN0IGNvbm5lY3Rpb24gPSBCdWlsZGVyLmFwcC5nZXRDb25uZWN0aW9uRGF0YSggJHRhc2sgKS5jb25uZWN0aW9uO1xuXHRsZXQgY3VycmVudFZhbHVlID0gY29ubmVjdGlvbi5vbl9kdXBsaWNhdGUgPz8gJ2luYyc7XG5cblx0aWYgKCBvbkR1cGxpY2F0ZU9wdGlvbi5sZW5ndGggKSB7XG5cdFx0Y3VycmVudFZhbHVlID0gb25EdXBsaWNhdGVPcHRpb24udmFsKCk7XG5cdH1cblxuXHQkY29udGFpbmVyLmh0bWwoIHRlbXBsYXRlKCB7XG5cdFx0Y29ubmVjdGlvbixcblx0XHRvbkR1cGxpY2F0ZU9wdGlvbnM6IHsgLi4uIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5vbkR1cGxpY2F0ZU9wdGlvbnMgfSxcblx0XHRwcm92aWRlcjogRU5USVRZX1NMVUcsXG5cdFx0ZXhwb3J0VG8sXG5cdFx0ZmlsZUZvcm1hdCxcblx0XHRjdXJyZW50VmFsdWUsXG5cdH0gKSApO1xufVxuIl0sIm1hcHBpbmdzIjoiOzs7Ozs7QUFFQSxJQUFBQSxVQUFBLEdBQUFDLE9BQUE7QUFBeUUsU0FBQUMsUUFBQUMsQ0FBQSxzQ0FBQUQsT0FBQSx3QkFBQUUsTUFBQSx1QkFBQUEsTUFBQSxDQUFBQyxRQUFBLGFBQUFGLENBQUEsa0JBQUFBLENBQUEsZ0JBQUFBLENBQUEsV0FBQUEsQ0FBQSx5QkFBQUMsTUFBQSxJQUFBRCxDQUFBLENBQUFHLFdBQUEsS0FBQUYsTUFBQSxJQUFBRCxDQUFBLEtBQUFDLE1BQUEsQ0FBQUcsU0FBQSxxQkFBQUosQ0FBQSxLQUFBRCxPQUFBLENBQUFDLENBQUE7QUFBQSxTQUFBSyxRQUFBQyxDQUFBLEVBQUFDLENBQUEsUUFBQUMsQ0FBQSxHQUFBQyxNQUFBLENBQUFDLElBQUEsQ0FBQUosQ0FBQSxPQUFBRyxNQUFBLENBQUFFLHFCQUFBLFFBQUFYLENBQUEsR0FBQVMsTUFBQSxDQUFBRSxxQkFBQSxDQUFBTCxDQUFBLEdBQUFDLENBQUEsS0FBQVAsQ0FBQSxHQUFBQSxDQUFBLENBQUFZLE1BQUEsV0FBQUwsQ0FBQSxXQUFBRSxNQUFBLENBQUFJLHdCQUFBLENBQUFQLENBQUEsRUFBQUMsQ0FBQSxFQUFBTyxVQUFBLE9BQUFOLENBQUEsQ0FBQU8sSUFBQSxDQUFBQyxLQUFBLENBQUFSLENBQUEs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
},{"./constants":4}],13:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.scheduleSetFrequencyChoices = exports.scheduleRender = exports.scheduleBindEvents = void 0;
var _constants = require("./constants");
var _utils = require("./utils");
/* global WPFormsChocolateChoices, wpformsEntryAutomationBuilderVars, wpf */
/**
 * Schedule-related functions for Entry Automation.
 *
 * @since 1.0.0
 */

var $ = jQuery;

/**
 * Updates and sets the frequency choices in the cache based on the provided frequency value.
 *
 * @return {void} Does not return any value.
 */
var scheduleSetFrequencyChoices = exports.scheduleSetFrequencyChoices = function scheduleSetFrequencyChoices() {
  var $this = $(this);
  var $task = $this.closest(_constants.selectors.task);
  var frequencyValue = $this.val();
  var $daysWrapper = $(_constants.selectors.daysWrapper, $task);
  var showDaysChoices = wpformsEntryAutomationBuilderVars.frequencyDaysLabels.hasOwnProperty(frequencyValue);
  $daysWrapper.toggleClass('wpforms-hidden', !showDaysChoices);
  if (!showDaysChoices) {
    return;
  }
  var blockClass = 'wpforms-entry-automation-schedule-days-block';
  var activeBlockClass = "".concat(blockClass, "-").concat(frequencyValue);
  var $activeBlock = $daysWrapper.find(".".concat(activeBlockClass));

  // Hide all blocks and disable inputs to prevent submission of hidden values.
  $daysWrapper.children(".".concat(blockClass)).addClass('wpforms-hidden').find(':input').prop('disabled', true);
  if ($activeBlock.length) {
    $activeBlock.removeClass('wpforms-hidden').find(':input').prop('disabled', false);
    return;
  }
  var daysTemplates = _constants.Templates.get("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-schedule-days"));

  // Generate and append the new block.
  var html = daysTemplates({
    frequencyDaysLabels: wpformsEntryAutomationBuilderVars.frequencyDaysLabels,
    frequencyValue: frequencyValue
  });

  // language=JavaScript
  $activeBlock = $('<div>').addClass("".concat(blockClass, " ").concat(activeBlockClass)).html(html);
  $daysWrapper.append($activeBlock);

  // Initialize chocolate choices for all chocolate choices containers.
  $('.wpforms-chocolate-choices', $activeBlock).each(function (index, element) {
    var _connection$schedule$, _connection$schedule;
    var connectionId = _constants.Builder.app.getConnectionId($task);
    var _Builder$app$getConne = _constants.Builder.app.getConnectionData($task),
      connection = _Builder$app$getConne.connection;
    WPFormsChocolateChoices.init($(element), {
      name: "settings[".concat(_constants.ENTITY_SLUG, "][").concat(connectionId, "][schedule][days][{index}]"),
      choices: _constants.frequencyChoices[frequencyValue],
      selected: (_connection$schedule$ = (_connection$schedule = connection.schedule) === null || _connection$schedule === void 0 ? void 0 : _connection$schedule.days) !== null && _connection$schedule$ !== void 0 ? _connection$schedule$ : []
    });
  });
  wpf.initTooltips($activeBlock);
};

/**
 * Updates the start and end dates to ensure they are in the correct order.
 *
 * @since 1.0.0
 *
 * @param {string} type         The type of date being updated ('start' or 'end').
 * @param {Object} startDateObj The Date object representing the start date.
 * @param {string} startDate    The string representation of the start date.
 * @param {Object} endDateObj   The Date object representing the end date.
 * @param {string} endDate      The string representation of the end date.
 * @param {Object} flatpickrJS  The instance of the flatpickr datepicker.
 */
var updateDates = function updateDates(type, startDateObj, startDate, endDateObj, endDate, flatpickrJS) {
  // Make sure the start date is before the end date and not in the past.
  if (type === 'start') {
    if (startDateObj < new Date()) {
      // If the start date is before today, set the start date today.
      flatpickrJS.setDate(new Date());
    }

    // If the start date is after the end date, set the start date to the end date.
    if (startDateObj > endDateObj) {
      flatpickrJS.setDate(endDate);
    }
  }

  // Make sure the end date is after the start date.
  if (type === 'end' && endDateObj < startDateObj) {
    // If the end date is before the start date, set the end date to the start date.
    flatpickrJS.setDate(startDate);
  }
};

/**
 * Handles the datepicker change event.
 *
 * @since 1.0.0
 *
 * @param {Object} event       The event object.
 * @param {Object} $element    The jQuery-wrapped DOM element that triggered the event.
 * @param {string} dateStr     The selected date string in the specified format.
 * @param {Object} flatpickrJS The instance of the flatpickr datepicker.
 */
var handleDatepickerChange = function handleDatepickerChange(event, $element, dateStr, flatpickrJS) {
  var taskId = $(this).data('id');
  var startDate = $("#wpforms-entry-automation-schedule-start-".concat(taskId)).val();
  var endDate = $("#wpforms-entry-automation-schedule-end-".concat(taskId)).val();
  var startDateObj = new Date(startDate);
  var endDateObj = new Date(endDate);
  if (!startDate && !endDate) {
    return;
  }
  var type = $(this).data('type');
  updateDates(type, startDateObj, startDate, endDateObj, endDate, flatpickrJS);
};

/**
 * Binds event listeners to the specified task element.
 *
 * @param {Object} $task The jQuery-wrapped DOM element representing the task.
 */
var scheduleBindEvents = exports.scheduleBindEvents = function scheduleBindEvents($task) {
  $(_constants.selectors.scheduleOptions, $task).on('change', _constants.selectors.frequency, scheduleSetFrequencyChoices);
  $('.wpforms-entry-automation-datepicker-wrap input').on('wpformsEntryAutomationDatepickerChange', handleDatepickerChange);
};
var flatPickerInstances = [];

/**
 * Renders the schedule options for the given task by loading the appropriate template.
 *
 * @since 1.0.0
 *
 * @param {Object} $task The jQuery element representing the task for which the schedule options are rendered.
 */
var scheduleRender = exports.scheduleRender = function scheduleRender($task) {
  // Load the specific template from Templates.
  var scheduleTemplate = _constants.Templates.get("wpforms-".concat(_constants.ENTITY_SLUG, "-builder-content-task-schedule"));
  var _Builder$app$getConne2 = _constants.Builder.app.getConnectionData($task),
    connection = _Builder$app$getConne2.connection;
  var connections = _constants.Builder.app.getConnections();
  var firstConnection = connections[0];
  if ($(firstConnection).data('connection_id') === connection.id) {
    connection.isFirst = true;
  }
  $(_constants.selectors.scheduleOptions, $task).html(scheduleTemplate({
    connection: connection,
    frequencyOptions: wpformsEntryAutomationBuilderVars.frequencyOptions,
    timeFormatOptions: wpformsEntryAutomationBuilderVars.timeFormatOptions,
    provider: _constants.ENTITY_SLUG
  }));

  // Clean up old date picker instances before creating new ones.
  // Without this cleanup, each time we create a new date picker, it adds hidden calendar elements to the page.
  // This prevents unnecessary page bloat and keeps the page running smoothly.
  flatPickerInstances.forEach(function (instance) {
    if (instance !== null && instance !== void 0 && instance.altInput && !document.contains(instance.altInput)) {
      instance.destroy();
    }
  });
  $(_constants.selectors.datePicker, $task).each(function () {
    flatPickerInstances.push((0, _utils.flatpickrInit)($(this)));
  });
  scheduleSetFrequencyChoices.call($(_constants.selectors.frequency, $task));
  scheduleBindEvents($task);
};
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJfY29uc3RhbnRzIiwicmVxdWlyZSIsIl91dGlscyIsIiQiLCJqUXVlcnkiLCJzY2hlZHVsZVNldEZyZXF1ZW5jeUNob2ljZXMiLCJleHBvcnRzIiwiJHRoaXMiLCIkdGFzayIsImNsb3Nlc3QiLCJzZWxlY3RvcnMiLCJ0YXNrIiwiZnJlcXVlbmN5VmFsdWUiLCJ2YWwiLCIkZGF5c1dyYXBwZXIiLCJkYXlzV3JhcHBlciIsInNob3dEYXlzQ2hvaWNlcyIsIndwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycyIsImZyZXF1ZW5jeURheXNMYWJlbHMiLCJoYXNPd25Qcm9wZXJ0eSIsInRvZ2dsZUNsYXNzIiwiYmxvY2tDbGFzcyIsImFjdGl2ZUJsb2NrQ2xhc3MiLCJjb25jYXQiLCIkYWN0aXZlQmxvY2siLCJmaW5kIiwiY2hpbGRyZW4iLCJhZGRDbGFzcyIsInByb3AiLCJsZW5ndGgiLCJyZW1vdmVDbGFzcyIsImRheXNUZW1wbGF0ZXMiLCJUZW1wbGF0ZXMiLCJnZXQiLCJFTlRJVFlfU0xVRyIsImh0bWwiLCJhcHBlbmQiLCJlYWNoIiwiaW5kZXgiLCJlbGVtZW50IiwiX2Nvbm5lY3Rpb24kc2NoZWR1bGUkIiwiX2Nvbm5lY3Rpb24kc2NoZWR1bGUiLCJjb25uZWN0aW9uSWQiLCJCdWlsZGVyIiwiYXBwIiwiZ2V0Q29ubmVjdGlvbklkIiwiX0J1aWxkZXIkYXBwJGdldENvbm5lIiwiZ2V0Q29ubmVjdGlvbkRhdGEiLCJjb25uZWN0aW9uIiwiV1BGb3Jtc0Nob2NvbGF0ZUNob2ljZXMiLCJpbml0IiwibmFtZSIsImNob2ljZXMiLCJmcmVxdWVuY3lDaG9pY2VzIiwic2VsZWN0ZWQiLCJzY2hlZHVsZSIsImRheXMiLCJ3cGYiLCJpbml0VG9vbHRpcHMiLCJ1cGRhdGVEYXRlcyIsInR5cGUiLCJzdGFydERhdGVPYmoiLCJzdGFydERhdGUiLCJlbmREYXRlT2JqIiwiZW5kRGF0ZSIsImZsYXRwaWNrckpTIiwiRGF0ZSIsInNldERhdGUiLCJoYW5kbGVEYXRlcGlja2VyQ2hhbmdlIiwiZXZlbnQiLCIkZWxlbWVudCIsImRhdGVTdHIiLCJ0YXNrSWQiLCJkYXRhIiwic2NoZWR1bGVCaW5kRXZlbnRzIiwic2NoZWR1bGVPcHRpb25zIiwib24iLCJmcmVxdWVuY3kiLCJmbGF0UGlja2VySW5zdGFuY2VzIiwic2NoZWR1bGVSZW5kZXIiLCJzY2hlZHVsZVRlbXBsYXRlIiwiX0J1aWxkZXIkYXBwJGdldENvbm5lMiIsImNvbm5lY3Rpb25zIiwiZ2V0Q29ubmVjdGlvbnMiLCJmaXJzdENvbm5lY3Rpb24iLCJpZCIsImlzRmlyc3QiLCJmcmVxdWVuY3lPcHRpb25zIiwidGltZUZvcm1hdE9wdGlvbnMiLCJwcm92aWRlciIsImZvckVhY2giLCJpbnN0YW5jZSIsImFsdElucHV0IiwiZG9jdW1lbnQiLCJjb250YWlucyIsImRlc3Ryb3kiLCJkYXRlUGlja2VyIiwicHVzaCIsImZsYXRwaWNrckluaXQiLCJjYWxsIl0sInNvdXJjZXMiOlsic2NoZWR1bGUuanMiXSwic291cmNlc0NvbnRlbnQiOlsiLyogZ2xvYmFsIFdQRm9ybXNDaG9jb2xhdGVDaG9pY2VzLCB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMsIHdwZiAqL1xuLyoqXG4gKiBTY2hlZHVsZS1yZWxhdGVkIGZ1bmN0aW9ucyBmb3IgRW50cnkgQXV0b21hdGlvbi5cbiAqXG4gKiBAc2luY2UgMS4wLjBcbiAqL1xuaW1wb3J0IHtcblx0VGVtcGxhdGVzLFxuXHRzZWxlY3RvcnMsXG5cdGZyZXF1ZW5jeUNob2ljZXMsXG5cdEVOVElUWV9TTFVHLFxuXHRCdWlsZGVyLFxufSBmcm9tICcuL2NvbnN0YW50cyc7XG5pbXBvcnQgeyBmbGF0cGlja3JJbml0IH0gZnJvbSAnLi91dGlscyc7XG5cbmNvbnN0ICQgPSBqUXVlcnk7XG5cbi8qKlxuICogVXBkYXRlcyBhbmQgc2V0cyB0aGUgZnJlcXVlbmN5IGNob2ljZXMgaW4gdGhlIGNhY2hlIGJhc2VkIG9uIHRoZSBwcm92aWRlZCBmcmVxdWVuY3kgdmFsdWUuXG4gKlxuICogQHJldHVybiB7dm9pZH0gRG9lcyBub3QgcmV0dXJuIGFueSB2YWx1ZS5cbiAqL1xuZXhwb3J0IGNvbnN0IHNjaGVkdWxlU2V0RnJlcXVlbmN5Q2hvaWNlcyA9IGZ1bmN0aW9uKCkge1xuXHRjb25zdCAkdGhpcyA9ICQoIHRoaXMgKTtcblx0Y29uc3QgJHRhc2sgPSAkdGhpcy5jbG9zZXN0KCBzZWxlY3RvcnMudGFzayApO1xuXHRjb25zdCBmcmVxdWVuY3lWYWx1ZSA9ICR0aGlzLnZhbCgpO1xuXHRjb25zdCAkZGF5c1dyYXBwZXIgPSAkKCBzZWxlY3RvcnMuZGF5c1dyYXBwZXIsICR0YXNrICk7XG5cdGNvbnN0IHNob3dEYXlzQ2hvaWNlcyA9IHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5mcmVxdWVuY3lEYXlzTGFiZWxzLmhhc093blByb3BlcnR5KCBmcmVxdWVuY3lWYWx1ZSApO1xuXG5cdCRkYXlzV3JhcHBlci50b2dnbGVDbGFzcyggJ3dwZm9ybXMtaGlkZGVuJywgISBzaG93RGF5c0Nob2ljZXMgKTtcblxuXHRpZiAoICEgc2hvd0RheXNDaG9pY2VzICkge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdGNvbnN0IGJsb2NrQ2xhc3MgPSAnd3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLXNjaGVkdWxlLWRheXMtYmxvY2snO1xuXHRjb25zdCBhY3RpdmVCbG9ja0NsYXNzID0gYCR7IGJsb2NrQ2xhc3MgfS0keyBmcmVxdWVuY3lWYWx1ZSB9YDtcblx0bGV0ICRhY3RpdmVCbG9jayA9ICRkYXlzV3JhcHBlci5maW5kKCBgLiR7IGFjdGl2ZUJsb2NrQ2xhc3MgfWAgKTtcblxuXHQvLyBIaWRlIGFsbCBibG9ja3MgYW5kIGRpc2FibGUgaW5wdXRzIHRvIHByZXZlbnQgc3VibWlzc2lvbiBvZiBoaWRkZW4gdmFsdWVzLlxuXHQkZGF5c1dyYXBwZXIuY2hpbGRyZW4oIGAuJHsgYmxvY2tDbGFzcyB9YCApXG5cdFx0LmFkZENsYXNzKCAnd3Bmb3Jtcy1oaWRkZW4nIClcblx0XHQuZmluZCggJzppbnB1dCcgKS5wcm9wKCAnZGlzYWJsZWQnLCB0cnVlICk7XG5cblx0aWYgKCAkYWN0aXZlQmxvY2subGVuZ3RoICkge1xuXHRcdCRhY3RpdmVCbG9jay5yZW1vdmVDbGFzcyggJ3dwZm9ybXMtaGlkZGVuJyApXG5cdFx0XHQuZmluZCggJzppbnB1dCcgKS5wcm9wKCAnZGlzYWJsZWQnLCBmYWxzZSApO1xuXG5cdFx0cmV0dXJuO1xuXHR9XG5cblx0Y29uc3QgZGF5c1RlbXBsYXRlcyA9IFRlbXBsYXRlcy5nZXQoIGB3cGZvcm1zLSR7IEVOVElUWV9TTFVHIH0tYnVpbGRlci1jb250ZW50LXRhc2stc2NoZWR1bGUtZGF5c2AgKTtcblxuXHQvLyBHZW5lcmF0ZSBhbmQgYXBwZW5kIHRoZSBuZXcgYmxvY2suXG5cdGNvbnN0IGh0bWwgPSBkYXlzVGVtcGxhdGVzKCB7XG5cdFx0ZnJlcXVlbmN5RGF5c0xhYmVsczogd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkJ1aWxkZXJWYXJzLmZyZXF1ZW5jeURheXNMYWJlbHMsXG5cdFx0ZnJlcXVlbmN5VmFsdWUsXG5cdH0gKTtcblxuXHQvLyBsYW5ndWFnZT1KYXZhU2NyaXB0XG5cdCRhY3RpdmVCbG9jayA9ICQoICc8ZGl2PicgKVxuXHRcdC5hZGRDbGFzcyggYCR7IGJsb2NrQ2xhc3MgfSAkeyBhY3RpdmVCbG9ja0NsYXNzIH1gIClcblx0XHQuaHRtbCggaHRtbCApO1xuXG5cdCRkYXlzV3JhcHBlci5hcHBlbmQoICRhY3RpdmVCbG9jayApO1xuXG5cdC8vIEluaXRpYWxpemUgY2hvY29sYXRlIGNob2ljZXMgZm9yIGFsbCBjaG9jb2xhdGUgY2hvaWNlcyBjb250YWluZXJzLlxuXHQkKCAnLndwZm9ybXMtY2hvY29sYXRlLWNob2ljZXMnLCAkYWN0aXZlQmxvY2sgKS5lYWNoKCAoIGluZGV4LCBlbGVtZW50ICkgPT4ge1xuXHRcdGNvbnN0IGNvbm5lY3Rpb25JZCA9IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25JZCggJHRhc2sgKTtcblx0XHRjb25zdCB7IGNvbm5lY3Rpb24gfSA9IEJ1aWxkZXIuYXBwLmdldENvbm5lY3Rpb25EYXRhKCAkdGFzayApO1xuXG5cdFx0V1BGb3Jtc0Nob2NvbGF0ZUNob2ljZXMuaW5pdCggJCggZWxlbWVudCApLCB7XG5cdFx0XHRuYW1lOiBgc2V0dGluZ3NbJHsgRU5USVRZX1NMVUcgfV1bJHsgY29ubmVjdGlvbklkIH1dW3NjaGVkdWxlXVtkYXlzXVt7aW5kZXh9XWAsXG5cdFx0XHRjaG9pY2VzOiBmcmVxdWVuY3lDaG9pY2VzWyBmcmVxdWVuY3lWYWx1ZSBdLFxuXHRcdFx0c2VsZWN0ZWQ6IGNvbm5lY3Rpb24uc2NoZWR1bGU/LmRheXMgPz8gW10sXG5cdFx0fSApO1xuXHR9ICk7XG5cblx0d3BmLmluaXRUb29sdGlwcyggJGFjdGl2ZUJsb2NrICk7XG59O1xuXG4vKipcbiAqIFVwZGF0ZXMgdGhlIHN0YXJ0IGFuZCBlbmQgZGF0ZXMgdG8gZW5zdXJlIHRoZXkgYXJlIGluIHRoZSBjb3JyZWN0IG9yZGVyLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlICAgICAgICAgVGhlIHR5cGUgb2YgZGF0ZSBiZWluZyB1cGRhdGVkICgnc3RhcnQnIG9yICdlbmQnKS5cbiAqIEBwYXJhbSB7T2JqZWN0fSBzdGFydERhdGVPYmogVGhlIERhdGUgb2JqZWN0IHJlcHJlc2VudGluZyB0aGUgc3RhcnQgZGF0ZS5cbiAqIEBwYXJhbSB7c3RyaW5nfSBzdGFydERhdGUgICAgVGhlIHN0cmluZyByZXByZXNlbnRhdGlvbiBvZiB0aGUgc3RhcnQgZGF0ZS5cbiAqIEBwYXJhbSB7T2JqZWN0fSBlbmREYXRlT2JqICAgVGhlIERhdGUgb2JqZWN0IHJlcHJlc2VudGluZyB0aGUgZW5kIGRhdGUuXG4gKiBAcGFyYW0ge3N0cmluZ30gZW5kRGF0ZSAgICAgIFRoZSBzdHJpbmcgcmVwcmVzZW50YXRpb24gb2YgdGhlIGVuZCBkYXRlLlxuICogQHBhcmFtIHtPYmplY3R9IGZsYXRwaWNrckpTICBUaGUgaW5zdGFuY2Ugb2YgdGhlIGZsYXRwaWNrciBkYXRlcGlja2VyLlxuICovXG5jb25zdCB1cGRhdGVEYXRlcyA9IGZ1bmN0aW9uKCB0eXBlLCBzdGFydERhdGVPYmosIHN0YXJ0RGF0ZSwgZW5kRGF0ZU9iaiwgZW5kRGF0ZSwgZmxhdHBpY2tySlMgKSB7XG5cdC8vIE1ha2Ugc3VyZSB0aGUgc3RhcnQgZGF0ZSBpcyBiZWZvcmUgdGhlIGVuZCBkYXRlIGFuZCBub3QgaW4gdGhlIHBhc3QuXG5cdGlmICggdHlwZSA9PT0gJ3N0YXJ0JyApIHtcblx0XHRpZiAoIHN0YXJ0RGF0ZU9iaiA8IG5ldyBEYXRlKCkgKSB7XG5cdFx0XHQvLyBJZiB0aGUgc3RhcnQgZGF0ZSBpcyBiZWZvcmUgdG9kYXksIHNldCB0aGUgc3RhcnQgZGF0ZSB0b2RheS5cblx0XHRcdGZsYXRwaWNrckpTLnNldERhdGUoIG5ldyBEYXRlKCkgKTtcblx0XHR9XG5cblx0XHQvLyBJZiB0aGUgc3RhcnQgZGF0ZSBpcyBhZnRlciB0aGUgZW5kIGRhdGUsIHNldCB0aGUgc3RhcnQgZGF0ZSB0byB0aGUgZW5kIGRhdGUuXG5cdFx0aWYgKCBzdGFydERhdGVPYmogPiBlbmREYXRlT2JqICkge1xuXHRcdFx0ZmxhdHBpY2tySlMuc2V0RGF0ZSggZW5kRGF0ZSApO1xuXHRcdH1cblx0fVxuXG5cdC8vIE1ha2Ugc3VyZSB0aGUgZW5kIGRhdGUgaXMgYWZ0ZXIgdGhlIHN0YXJ0IGRhdGUuXG5cdGlmICggdHlwZSA9PT0gJ2VuZCcgJiYgZW5kRGF0ZU9iaiA8IHN0YXJ0RGF0ZU9iaiApIHtcblx0XHQvLyBJZiB0aGUgZW5kIGRhdGUgaXMgYmVmb3JlIHRoZSBzdGFydCBkYXRlLCBzZXQgdGhlIGVuZCBkYXRlIHRvIHRoZSBzdGFydCBkYXRlLlxuXHRcdGZsYXRwaWNrckpTLnNldERhdGUoIHN0YXJ0RGF0ZSApO1xuXHR9XG59O1xuXG4vKipcbiAqIEhhbmRsZXMgdGhlIGRhdGVwaWNrZXIgY2hhbmdlIGV2ZW50LlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSBldmVudCAgICAgICBUaGUgZXZlbnQgb2JqZWN0LlxuICogQHBhcmFtIHtPYmplY3R9ICRlbGVtZW50ICAgIFRoZSBqUXVlcnktd3JhcHBlZCBET00gZWxlbWVudCB0aGF0IHRyaWdnZXJlZCB0aGUgZXZlbnQuXG4gKiBAcGFyYW0ge3N0cmluZ30gZGF0ZVN0ciAgICAgVGhlIHNlbGVjdGVkIGRhdGUgc3RyaW5nIGluIHRoZSBzcGVjaWZpZWQgZm9ybWF0LlxuICogQHBhcmFtIHtPYmplY3R9IGZsYXRwaWNrckpTIFRoZSBpbnN0YW5jZSBvZiB0aGUgZmxhdHBpY2tyIGRhdGVwaWNrZXIuXG4gKi9cbmNvbnN0IGhhbmRsZURhdGVwaWNrZXJDaGFuZ2UgPSBmdW5jdGlvbiggZXZlbnQsICRlbGVtZW50LCBkYXRlU3RyLCBmbGF0cGlja3JKUyApIHtcblx0Y29uc3QgdGFza0lkID0gJCggdGhpcyApLmRhdGEoICdpZCcgKTtcblx0Y29uc3Qgc3RhcnREYXRlID0gJCggYCN3cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tc2NoZWR1bGUtc3RhcnQtJHsgdGFza0lkIH1gICkudmFsKCk7XG5cdGNvbnN0IGVuZERhdGUgPSAkKCBgI3dwZm9ybXMtZW50cnktYXV0b21hdGlvbi1zY2hlZHVsZS1lbmQtJHsgdGFza0lkIH1gICkudmFsKCk7XG5cblx0Y29uc3Qgc3RhcnREYXRlT2JqID0gbmV3IERhdGUoIHN0YXJ0RGF0ZSApO1xuXHRjb25zdCBlbmREYXRlT2JqID0gbmV3IERhdGUoIGVuZERhdGUgKTtcblxuXHRpZiAoICEgc3RhcnREYXRlICYmICEgZW5kRGF0ZSApIHtcblx0XHRyZXR1cm47XG5cdH1cblxuXHRjb25zdCB0eXBlID0gJCggdGhpcyApLmRhdGEoICd0eXBlJyApO1xuXG5cdHVwZGF0ZURhdGVzKCB0eXBlLCBzdGFydERhdGVPYmosIHN0YXJ0RGF0ZSwgZW5kRGF0ZU9iaiwgZW5kRGF0ZSwgZmxhdHBpY2tySlMgKTtcbn07XG5cbi8qKlxuICogQmluZHMgZXZlbnQgbGlzdGVuZXJzIHRvIHRoZSBzcGVjaWZpZWQgdGFzayBlbGVtZW50LlxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSAkdGFzayBUaGUgalF1ZXJ5LXdyYXBwZWQgRE9NIGVsZW1lbnQgcmVwcmVzZW50aW5nIHRoZSB0YXNrLlxuICovXG5leHBvcnQgY29uc3Qgc2NoZWR1bGVCaW5kRXZlbnRzID0gZnVuY3Rpb24oICR0YXNrICkge1xuXHQkKCBzZWxlY3RvcnMuc2NoZWR1bGVPcHRpb25zLCAkdGFzayApLm9uKCAnY2hhbmdlJywgc2VsZWN0b3JzLmZyZXF1ZW5jeSwgc2NoZWR1bGVTZXRGcmVxdWVuY3lDaG9pY2VzICk7XG5cblx0JCggJy53cGZvcm1zLWVudHJ5LWF1dG9tYXRpb24tZGF0ZXBpY2tlci13cmFwIGlucHV0JyApLm9uKCAnd3Bmb3Jtc0VudHJ5QXV0b21hdGlvbkRhdGVwaWNrZXJDaGFuZ2UnLCBoYW5kbGVEYXRlcGlja2VyQ2hhbmdlICk7XG59O1xuXG5jb25zdCBmbGF0UGlja2VySW5zdGFuY2VzID0gW107XG5cbi8qKlxuICogUmVuZGVycyB0aGUgc2NoZWR1bGUgb3B0aW9ucyBmb3IgdGhlIGdpdmVuIHRhc2sgYnkgbG9hZGluZyB0aGUgYXBwcm9wcmlhdGUgdGVtcGxhdGUuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtPYmplY3R9ICR0YXNrIFRoZSBqUXVlcnkgZWxlbWVudCByZXByZXNlbnRpbmcgdGhlIHRhc2sgZm9yIHdoaWNoIHRoZSBzY2hlZHVsZSBvcHRpb25zIGFyZSByZW5kZXJlZC5cbiAqL1xuZXhwb3J0IGNvbnN0IHNjaGVkdWxlUmVuZGVyID0gZnVuY3Rpb24oICR0YXNrICkge1xuXHQvLyBMb2FkIHRoZSBzcGVjaWZpYyB0ZW1wbGF0ZSBmcm9tIFRlbXBsYXRlcy5cblx0Y29uc3Qgc2NoZWR1bGVUZW1wbGF0ZSA9IFRlbXBsYXRlcy5nZXQoIGB3cGZvcm1zLSR7IEVOVElUWV9TTFVHIH0tYnVpbGRlci1jb250ZW50LXRhc2stc2NoZWR1bGVgICk7XG5cdGNvbnN0IHsgY29ubmVjdGlvbiB9ID0gQnVpbGRlci5hcHAuZ2V0Q29ubmVjdGlvbkRhdGEoICR0YXNrICk7XG5cblx0Y29uc3QgY29ubmVjdGlvbnMgPSBCdWlsZGVyLmFwcC5nZXRDb25uZWN0aW9ucygpO1xuXHRjb25zdCBmaXJzdENvbm5lY3Rpb24gPSBjb25uZWN0aW9uc1sgMCBdO1xuXHRpZiAoICQoIGZpcnN0Q29ubmVjdGlvbiApLmRhdGEoICdjb25uZWN0aW9uX2lkJyApID09PSBjb25uZWN0aW9uLmlkICkge1xuXHRcdGNvbm5lY3Rpb24uaXNGaXJzdCA9IHRydWU7XG5cdH1cblxuXHQkKCBzZWxlY3RvcnMuc2NoZWR1bGVPcHRpb25zLCAkdGFzayApLmh0bWwoIHNjaGVkdWxlVGVtcGxhdGUoIHtcblx0XHRjb25uZWN0aW9uLFxuXHRcdGZyZXF1ZW5jeU9wdGlvbnM6IHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5mcmVxdWVuY3lPcHRpb25zLFxuXHRcdHRpbWVGb3JtYXRPcHRpb25zOiB3cGZvcm1zRW50cnlBdXRvbWF0aW9uQnVpbGRlclZhcnMudGltZUZvcm1hdE9wdGlvbnMsXG5cdFx0cHJvdmlkZXI6IEVOVElUWV9TTFVHLFxuXHR9ICkgKTtcblxuXHQvLyBDbGVhbiB1cCBvbGQgZGF0ZSBwaWNrZXIgaW5zdGFuY2VzIGJlZm9yZSBjcmVhdGluZyBuZXcgb25lcy5cblx0Ly8gV2l0aG91dCB0aGlzIGNsZWFudXAsIGVhY2ggdGltZSB3ZSBjcmVhdGUgYSBuZXcgZGF0ZSBwaWNrZXIsIGl0IGFkZHMgaGlkZGVuIGNhbGVuZGFyIGVsZW1lbnRzIHRvIHRoZSBwYWdlLlxuXHQvLyBUaGlzIHByZXZlbnRzIHVubmVjZXNzYXJ5IHBhZ2UgYmxvYXQgYW5kIGtlZXBzIHRoZSBwYWdlIHJ1bm5pbmcgc21vb3RobHkuXG5cdGZsYXRQaWNrZXJJbnN0YW5jZXMuZm9yRWFjaCggKCBpbnN0YW5jZSApID0+IHtcblx0XHRpZiAoIGluc3RhbmNlPy5hbHRJbnB1dCAmJiAhIGRvY3VtZW50LmNvbnRhaW5zKCBpbnN0YW5jZS5hbHRJbnB1dCApICkge1xuXHRcdFx0aW5zdGFuY2UuZGVzdHJveSgpO1xuXHRcdH1cblx0fSApO1xuXG5cdCQoIHNlbGVjdG9ycy5kYXRlUGlja2VyLCAkdGFzayApLmVhY2goIGZ1bmN0aW9uKCkge1xuXHRcdGZsYXRQaWNrZXJJbnN0YW5jZXMucHVzaCggZmxhdHBpY2tySW5pdCggJCggdGhpcyApICkgKTtcblx0fSApO1xuXG5cdHNjaGVkdWxlU2V0RnJlcXVlbmN5Q2hvaWNlcy5jYWxsKCAkKCBzZWxlY3RvcnMuZnJlcXVlbmN5LCAkdGFzayApICk7XG5cdHNjaGVkdWxlQmluZEV2ZW50cyggJHRhc2sgKTtcbn07XG4iXSwibWFwcGluZ3MiOiI7Ozs7OztBQU1BLElBQUFBLFVBQUEsR0FBQUMsT0FBQTtBQU9BLElBQUFDLE1BQUEsR0FBQUQsT0FBQTtBQWJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFVQSxJQUFNRSxDQUFDLEdBQUdDLE1BQU07O0FBRWhCO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDTyxJQUFNQywyQkFBMkIsR0FBQUMsT0FBQSxDQUFBRCwyQkFBQSxHQUFHLFNBQTlCQSwyQkFBMkJBLENBQUEsRUFBYztFQUNyRCxJQUFNRSxLQUFLLEdBQUdKLENBQUMsQ0FBRSxJQUFLLENBQUM7RUFDdkIsSUFBTUssS0FBSyxHQUFHRCxLQUFLLENBQUNFLE9BQU8sQ0FBRUMsb0JBQVMsQ0FBQ0MsSUFBSyxDQUFDO0VBQzdDLElBQU1DLGNBQWMsR0FBR0wsS0FBSyxDQUFDTSxHQUFHLENBQUMsQ0FBQztFQUNsQyxJQUFNQyxZQUFZLEdBQUdYLENBQUMsQ0FBRU8sb0JBQVMsQ0FBQ0ssV0FBVyxFQUFFUCxLQUFNLENBQUM7RUFDdEQsSUFBTVEsZUFBZSxHQUFHQyxpQ0FBaUMsQ0FBQ0MsbUJBQW1CLENBQUNDLGNBQWMsQ0FBRVAsY0FBZSxDQUFDO0VBRTlHRSxZQUFZLENBQUNNLFdBQVcsQ0FBRSxnQkFBZ0IsRUFB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
},{"./constants":4,"./utils":14}],14:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.initChoicesJS = exports.getRandomId = exports.generateDaysArray = exports.flatpickrInit = void 0;
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/* global flatpickr, wpformsEntryAutomationBuilderVars, Choices, wpf, WPForms */

/**
 * Utility functions for Entry Automation.
 *
 * @since 1.0.0
 */

/**
 * Generates a random ID string.
 * The generated ID is based on the current timestamp and converted to a base-16 string.
 *
 * @since 1.0.0
 *
 * @return {string} A hexadecimal string representation of the current timestamp.
 */
var getRandomId = exports.getRandomId = function getRandomId() {
  var id = new Date().getTime().toString(16);

  // We shouldn't generate an ID that can be converted to a number.
  // So, if our ID contains non-numeric characters, return the ID as-is.
  if (Number.isNaN(Number(id))) {
    return id;
  }

  // Try to convert the ID to a base-16 string once again.
  return Number(id).toString(16);
};

/**
 * Generates an array of numbers from 1 to 31.
 *
 * @since 1.0.0
 *
 * @return {number[]} Array of numbers from 1 to 31.
 */
var generateDaysArray = exports.generateDaysArray = function generateDaysArray() {
  return Array.from({
    length: 31
  }, function (_, i) {
    return i + 1;
  });
};

/**
 * Initializes the flatpickr datepicker on the provided element with custom settings.
 *
 * @since 1.0.0
 *
 * @param {Object} $element The jQuery-wrapped DOM element to initialize flatpickr on.
 *
 * @return {flatpickr.Instance} The instance of the initialized flatpickr.
 */
var flatpickrInit = exports.flatpickrInit = function flatpickrInit($element) {
  var flatpickrJS = flatpickr($element, {
    altFormat: 'M j, Y',
    altInput: true,
    dateFormat: 'Y-m-d',
    locale: _objectSpread({}, flatpickr.l10ns[wpformsEntryAutomationBuilderVars.locale] || {}),
    onOpen: function onOpen() {
      $element.closest('.wpforms-entry-automation-datepicker-wrap').addClass('active');
    },
    onClose: function onClose() {
      $element.closest('.wpforms-entry-automation-datepicker-wrap').removeClass('active');
    },
    onChange: function onChange(selectedDates, dateStr) {
      /**
       * Trigger a custom event when the date is changed.
       * This allows for additional actions to be taken when the date is selected.
       *
       * @since 1.0.0
       *
       * @param {Object} $element    The jQuery-wrapped DOM element that triggered the event.
       * @param {string} dateStr     The selected date string in the specified format.
       * @param {Object} flatpickrJS The instance of the flatpickr datepicker.
       */
      $element.trigger('wpformsEntryAutomationDatepickerChange', [$element, dateStr, this]);
    }
  });
  $element.closest('.wpforms-entry-automation-datepicker-wrap').on('click', function () {
    flatpickrJS.open();
  });
  return flatpickrJS;
};

/**
 * Initializes the Choices.js library on the specified element.
 * This function is used to enhance select elements with search and multi-select capabilities.
 *
 * @since 1.0.0
 *
 * @param {Object} $task    The jQuery-wrapped DOM element representing the task.
 * @param {string} selector The CSS selector for the element to initialize Choices.js on.
 */
var initChoicesJS = exports.initChoicesJS = function initChoicesJS($task, selector) {
  // Load if the function exists.
  if (typeof window.Choices !== 'function') {
    return;
  }
  var $choices = jQuery(selector, $task);
  $choices.each(function (index, element) {
    var _WPForms$Admin$Builde;
    var $this = jQuery(element);
    if ('undefined' !== typeof $this.data('choicesjs')) {
      return;
    }
    (_WPForms$Admin$Builde = WPForms.Admin.Builder.UndoRedo) === null || _WPForms$Admin$Builde === void 0 || _WPForms$Admin$Builde.preventRecord(true);
    $this.data('choicesjs', new Choices($this[0], {
      shouldSort: false,
      removeItemButton: true,
      fuseOptions: {
        threshold: 0.1,
        distance: 1000
      },
      callbackOnInit: function callbackOnInit() {
        var _WPForms$Admin$Builde2;
        wpf.initMultipleSelectWithSearch(this);
        wpf.showMoreButtonForChoices(this.containerOuter.element);
        (_WPForms$Admin$Builde2 = WPForms.Admin.Builder.UndoRedo) === null || _WPForms$Admin$Builde2 === void 0 || _WPForms$Admin$Builde2.preventRecord('continue');
      }
    }));
  });
};
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJnZXRSYW5kb21JZCIsImV4cG9ydHMiLCJpZCIsIkRhdGUiLCJnZXRUaW1lIiwidG9TdHJpbmciLCJOdW1iZXIiLCJpc05hTiIsImdlbmVyYXRlRGF5c0FycmF5IiwiQXJyYXkiLCJmcm9tIiwibGVuZ3RoIiwiXyIsImkiLCJmbGF0cGlja3JJbml0IiwiJGVsZW1lbnQiLCJmbGF0cGlja3JKUyIsImZsYXRwaWNrciIsImFsdEZvcm1hdCIsImFsdElucHV0IiwiZGF0ZUZvcm1hdCIsImxvY2FsZSIsIl9vYmplY3RTcHJlYWQiLCJsMTBucyIsIndwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycyIsIm9uT3BlbiIsImNsb3Nlc3QiLCJhZGRDbGFzcyIsIm9uQ2xvc2UiLCJyZW1vdmVDbGFzcyIsIm9uQ2hhbmdlIiwic2VsZWN0ZWREYXRlcyIsImRhdGVTdHIiLCJ0cmlnZ2VyIiwib24iLCJvcGVuIiwiaW5pdENob2ljZXNKUyIsIiR0YXNrIiwic2VsZWN0b3IiLCJ3aW5kb3ciLCJDaG9pY2VzIiwiJGNob2ljZXMiLCJqUXVlcnkiLCJlYWNoIiwiaW5kZXgiLCJlbGVtZW50IiwiX1dQRm9ybXMkQWRtaW4kQnVpbGRlIiwiJHRoaXMiLCJkYXRhIiwiV1BGb3JtcyIsIkFkbWluIiwiQnVpbGRlciIsIlVuZG9SZWRvIiwicHJldmVudFJlY29yZCIsInNob3VsZFNvcnQiLCJyZW1vdmVJdGVtQnV0dG9uIiwiZnVzZU9wdGlvbnMiLCJ0aHJlc2hvbGQiLCJkaXN0YW5jZSIsImNhbGxiYWNrT25Jbml0IiwiX1dQRm9ybXMkQWRtaW4kQnVpbGRlMiIsIndwZiIsImluaXRNdWx0aXBsZVNlbGVjdFdpdGhTZWFyY2giLCJzaG93TW9yZUJ1dHRvbkZvckNob2ljZXMiLCJjb250YWluZXJPdXRlciJdLCJzb3VyY2VzIjpbInV0aWxzLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCBmbGF0cGlja3IsIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycywgQ2hvaWNlcywgd3BmLCBXUEZvcm1zICovXG5cbi8qKlxuICogVXRpbGl0eSBmdW5jdGlvbnMgZm9yIEVudHJ5IEF1dG9tYXRpb24uXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKi9cblxuLyoqXG4gKiBHZW5lcmF0ZXMgYSByYW5kb20gSUQgc3RyaW5nLlxuICogVGhlIGdlbmVyYXRlZCBJRCBpcyBiYXNlZCBvbiB0aGUgY3VycmVudCB0aW1lc3RhbXAgYW5kIGNvbnZlcnRlZCB0byBhIGJhc2UtMTYgc3RyaW5nLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEByZXR1cm4ge3N0cmluZ30gQSBoZXhhZGVjaW1hbCBzdHJpbmcgcmVwcmVzZW50YXRpb24gb2YgdGhlIGN1cnJlbnQgdGltZXN0YW1wLlxuICovXG5leHBvcnQgY29uc3QgZ2V0UmFuZG9tSWQgPSAoKSA9PiB7XG5cdGNvbnN0IGlkID0gbmV3IERhdGUoKS5nZXRUaW1lKCkudG9TdHJpbmcoIDE2ICk7XG5cblx0Ly8gV2Ugc2hvdWxkbid0IGdlbmVyYXRlIGFuIElEIHRoYXQgY2FuIGJlIGNvbnZlcnRlZCB0byBhIG51bWJlci5cblx0Ly8gU28sIGlmIG91ciBJRCBjb250YWlucyBub24tbnVtZXJpYyBjaGFyYWN0ZXJzLCByZXR1cm4gdGhlIElEIGFzLWlzLlxuXHRpZiAoIE51bWJlci5pc05hTiggTnVtYmVyKCBpZCApICkgKSB7XG5cdFx0cmV0dXJuIGlkO1xuXHR9XG5cblx0Ly8gVHJ5IHRvIGNvbnZlcnQgdGhlIElEIHRvIGEgYmFzZS0xNiBzdHJpbmcgb25jZSBhZ2Fpbi5cblx0cmV0dXJuIE51bWJlciggaWQgKS50b1N0cmluZyggMTYgKTtcbn07XG5cbi8qKlxuICogR2VuZXJhdGVzIGFuIGFycmF5IG9mIG51bWJlcnMgZnJvbSAxIHRvIDMxLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEByZXR1cm4ge251bWJlcltdfSBBcnJheSBvZiBudW1iZXJzIGZyb20gMSB0byAzMS5cbiAqL1xuZXhwb3J0IGNvbnN0IGdlbmVyYXRlRGF5c0FycmF5ID0gKCkgPT4gQXJyYXkuZnJvbSggeyBsZW5ndGg6IDMxIH0sICggXywgaSApID0+IGkgKyAxICk7XG5cbi8qKlxuICogSW5pdGlhbGl6ZXMgdGhlIGZsYXRwaWNrciBkYXRlcGlja2VyIG9uIHRoZSBwcm92aWRlZCBlbGVtZW50IHdpdGggY3VzdG9tIHNldHRpbmdzLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSAkZWxlbWVudCBUaGUgalF1ZXJ5LXdyYXBwZWQgRE9NIGVsZW1lbnQgdG8gaW5pdGlhbGl6ZSBmbGF0cGlja3Igb24uXG4gKlxuICogQHJldHVybiB7ZmxhdHBpY2tyLkluc3RhbmNlfSBUaGUgaW5zdGFuY2Ugb2YgdGhlIGluaXRpYWxpemVkIGZsYXRwaWNrci5cbiAqL1xuZXhwb3J0IGNvbnN0IGZsYXRwaWNrckluaXQgPSBmdW5jdGlvbiggJGVsZW1lbnQgKSB7XG5cdGNvbnN0IGZsYXRwaWNrckpTID0gZmxhdHBpY2tyKCAkZWxlbWVudCwge1xuXHRcdGFsdEZvcm1hdDogJ00gaiwgWScsXG5cdFx0YWx0SW5wdXQ6IHRydWUsXG5cdFx0ZGF0ZUZvcm1hdDogJ1ktbS1kJyxcblx0XHRsb2NhbGU6IHtcblx0XHRcdC8vIExvY2FsaXplZCBwZXItaW5zdGFuY2UsIGlmIGFwcGxpY2FibGUuXG5cdFx0XHQuLi5mbGF0cGlja3IubDEwbnNbIHdwZm9ybXNFbnRyeUF1dG9tYXRpb25CdWlsZGVyVmFycy5sb2NhbGUgXSB8fCB7fSxcblx0XHR9LFxuXHRcdG9uT3BlbigpIHtcblx0XHRcdCRlbGVtZW50LmNsb3Nlc3QoICcud3Bmb3Jtcy1lbnRyeS1hdXRvbWF0aW9uLWRhdGVwaWNrZXItd3JhcCcgKS5hZGRDbGFzcyggJ2FjdGl2ZScgKTtcblx0XHR9LFxuXHRcdG9uQ2xvc2UoKSB7XG5cdFx0XHQkZWxlbWVudC5jbG9zZXN0KCAnLndwZm9ybXMtZW50cnktYXV0b21hdGlvbi1kYXRlcGlja2VyLXdyYXAnICkucmVtb3ZlQ2xhc3MoICdhY3RpdmUnICk7XG5cdFx0fSxcblx0XHRvbkNoYW5nZSggc2VsZWN0ZWREYXRlcywgZGF0ZVN0ciApIHtcblx0XHRcdC8qKlxuXHRcdFx0ICogVHJpZ2dlciBhIGN1c3RvbSBldmVudCB3aGVuIHRoZSBkYXRlIGlzIGNoYW5nZWQuXG5cdFx0XHQgKiBUaGlzIGFsbG93cyBmb3IgYWRkaXRpb25hbCBhY3Rpb25zIHRvIGJlIHRha2VuIHdoZW4gdGhlIGRhdGUgaXMgc2VsZWN0ZWQuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHNpbmNlIDEuMC4wXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtPYmplY3R9ICRlbGVtZW50ICAgIFRoZSBqUXVlcnktd3JhcHBlZCBET00gZWxlbWVudCB0aGF0IHRyaWdnZXJlZCB0aGUgZXZlbnQuXG5cdFx0XHQgKiBAcGFyYW0ge3N0cmluZ30gZGF0ZVN0ciAgICAgVGhlIHNlbGVjdGVkIGRhdGUgc3RyaW5nIGluIHRoZSBzcGVjaWZpZWQgZm9ybWF0LlxuXHRcdFx0ICogQHBhcmFtIHtPYmplY3R9IGZsYXRwaWNrckpTIFRoZSBpbnN0YW5jZSBvZiB0aGUgZmxhdHBpY2tyIGRhdGVwaWNrZXIuXG5cdFx0XHQgKi9cblx0XHRcdCRlbGVtZW50LnRyaWdnZXIoICd3cGZvcm1zRW50cnlBdXRvbWF0aW9uRGF0ZXBpY2tlckNoYW5nZScsIFsgJGVsZW1lbnQsIGRhdGVTdHIsIHRoaXMgXSApO1xuXHRcdH0sXG5cdH0gKTtcblxuXHQkZWxlbWVudC5jbG9zZXN0KCAnLndwZm9ybXMtZW50cnktYXV0b21hdGlvbi1kYXRlcGlja2VyLXdyYXAnICkub24oICdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdGZsYXRwaWNrckpTLm9wZW4oKTtcblx0fSApO1xuXG5cdHJldHVybiBmbGF0cGlja3JKUztcbn07XG5cbi8qKlxuICogSW5pdGlhbGl6ZXMgdGhlIENob2ljZXMuanMgbGlicmFyeSBvbiB0aGUgc3BlY2lmaWVkIGVsZW1lbnQuXG4gKiBUaGlzIGZ1bmN0aW9uIGlzIHVzZWQgdG8gZW5oYW5jZSBzZWxlY3QgZWxlbWVudHMgd2l0aCBzZWFyY2ggYW5kIG11bHRpLXNlbGVjdCBjYXBhYmlsaXRpZXMuXG4gKlxuICogQHNpbmNlIDEuMC4wXG4gKlxuICogQHBhcmFtIHtPYmplY3R9ICR0YXNrICAgIFRoZSBqUXVlcnktd3JhcHBlZCBET00gZWxlbWVudCByZXByZXNlbnRpbmcgdGhlIHRhc2suXG4gKiBAcGFyYW0ge3N0cmluZ30gc2VsZWN0b3IgVGhlIENTUyBzZWxlY3RvciBmb3IgdGhlIGVsZW1lbnQgdG8gaW5pdGlhbGl6ZSBDaG9pY2VzLmpzIG9uLlxuICovXG5leHBvcnQgY29uc3QgaW5pdENob2ljZXNKUyA9IGZ1bmN0aW9uKCAkdGFzaywgc2VsZWN0b3IgKSB7XG5cdC8vIExvYWQgaWYgdGhlIGZ1bmN0aW9uIGV4aXN0cy5cblx0aWYgKCB0eXBlb2Ygd2luZG93LkNob2ljZXMgIT09ICdmdW5jdGlvbicgKSB7XG5cdFx0cmV0dXJuO1xuXHR9XG5cblx0Y29uc3QgJGNob2ljZXMgPSBqUXVlcnkoIHNlbGVjdG9yLCAkdGFzayApO1xuXG5cdCRjaG9pY2VzLmVhY2goICggaW5kZXgsIGVsZW1lbnQgKSA9PiB7XG5cdFx0Y29uc3QgJHRoaXMgPSBqUXVlcnkoIGVsZW1lbnQgKTtcblxuXHRcdGlmICggJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiAkdGhpcy5kYXRhKCAnY2hvaWNlc2pzJyApICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdFdQRm9ybXMuQWRtaW4uQnVpbGRlci5VbmRvUmVkbz8ucHJldmVudFJlY29yZCggdHJ1ZSApO1xuXG5cdFx0JHRoaXMuZGF0YSggJ2Nob2ljZXNqcycsIG5ldyBDaG9pY2VzKCAkdGhpc1sgMCBdLCB7XG5cdFx0XHRzaG91bGRTb3J0OiBmYWxzZSxcblx0XHRcdHJlbW92ZUl0ZW1CdXR0b246IHRydWUsXG5cdFx0XHRmdXNlT3B0aW9uczoge1xuXHRcdFx0XHR0aHJlc2hvbGQ6IDAuMSxcblx0XHRcdFx0ZGlzdGFuY2U6IDEwMDAsXG5cdFx0XHR9LFxuXHRcdFx0Y2FsbGJhY2tPbkluaXQoKSB7XG5cdFx0XHRcdHdwZi5pbml0TXVsdGlwbGVTZWxlY3RXaXRoU2VhcmNoKCB0aGlzICk7XG5cdFx0XHRcdHdwZi5zaG93TW9yZUJ1dHRvbkZvckNob2ljZXMoIHRoaXMuY29udGFpbmVyT3V0ZXIuZWxlbWVudCApO1xuXHRcdFx0XHRXUEZvcm1zLkFkbWluLkJ1aWxkZXIuVW5kb1JlZG8/LnByZXZlbnRSZWNvcmQoICdjb250aW51ZScgKTtcblx0XHRcdH0sXG5cdFx0fSApICk7XG5cdH0gKTtcbn07XG4iXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7OztBQUFBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNPLElBQU1BLFdBQVcsR0FBQUMsT0FBQSxDQUFBRCxXQUFBLEdBQUcsU0FBZEEsV0FBV0EsQ0FBQSxFQUFTO0VBQ2hDLElBQU1FLEVBQUUsR0FBRyxJQUFJQyxJQUFJLENBQUMsQ0FBQyxDQUFDQyxPQUFPLENBQUMsQ0FBQyxDQUFDQyxRQUFRLENBQUUsRUFBRyxDQUFDOztFQUU5QztFQUNBO0VBQ0EsSUFBS0MsTUFBTSxDQUFDQyxLQUFLLENBQUVELE1BQU0sQ0FBRUosRUFBRyxDQUFFLENBQUMsRUFBRztJQUNuQyxPQUFPQSxFQUFFO0VBQ1Y7O0VBRUE7RUFDQSxPQUFPSSxNQUFNLENBQUVKLEVBQUcsQ0FBQyxDQUFDRyxRQUFRLENBQUUsRUFBRyxDQUFDO0FBQ25DLENBQUM7O0FBRUQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDTyxJQUFNRyxpQkFBaUIsR0FBQVAsT0FBQSxDQUFBTyxpQkFBQSxHQUFHLFNBQXBCQSxpQkFBaUJBLENBQUE7RUFBQSxPQUFTQyxLQUFLLENBQUNDLElBQUksQ0FBRTtJQUFFQyxNQUFNLEVBQUU7RUFBRyxDQUFDLEVBQUUsVUFBRUMsQ0FBQyxFQUFFQyxDQUFDO0lBQUEsT0FBTUEsQ0FBQyxHQUFHLENBQUM7RUFBQSxDQUFDLENBQUM7QUFBQTs7QUFFdEY7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ08sSUFBTUMsYUFBYSxHQUFBYixPQUFBLENBQUFhLGFBQUEsR0FBRyxTQUFoQkEsYUFBYUEsQ0FBYUMsUUFBUSxFQUFHO0VBQ2pELElBQU1DLFdBQVcsR0FBR0MsU0FBUyxDQUFFRixRQUFRLEVBQUU7SUFDeENHLFNBQVMsRUFBRSxRQUFRO0lBQ25CQyxRQUFRLEVBQUUsSUFBSTtJQUNkQyxVQUFVLEVBQUUsT0FBTztJQUNuQkMsTUFBTSxFQUFBQyxhQUFBLEtBRUZMLFNBQVMsQ0FBQ00sS0FBSyxDQUFFQyxpQ0FBaUMsQ0FBQ0gsTUFBTSxDQUFFLElBQUksQ0FBQyxDQUFDLENBQ3BFO0lBQ0RJLE1BQU0sV0FBTkEsTUFBTUEsQ0FBQSxFQUFHO01BQ1JWLFFBQVEsQ0FBQ1csT0FBTyxDQUFFLDJDQUE0QyxDQUFDLENBQUNDLFFBQVEsQ0FBRSxRQUFTLENBQUM7SUFDckYsQ0FBQztJQUNEQyxPQUFPLFdBQVBBLE9BQU9BLENBQUEsRUFBRztNQUNUYixRQUFRLENBQUNXLE9BQU8sQ0FBRSwyQ0FBNEMsQ0FBQyxDQUFDRyxXQUFXLENBQUUsUUFBUyxDQUFDO0lBQ3hGLENBQUM7SUFDREMsUUFBUSxXQUFSQSxRQUFRQSxDQUFFQyxhQUFhLEVBQUVDLE9BQU8sRUFBRztNQUNsQztBQUNIO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtNQUNHakIsUUFBUSxDQUFDa0IsT0FBTyxDQUFFLHdDQUF3QyxFQUFFLENBQUVsQixRQUFRLEVBQUVpQixPQUFPLEVBQUUsSUFBSSxDQUFHLENBQUM7SUFDMUY7RUFDRCxDQUFFLENBQUM7RUFFSGpCLFFBQVEsQ0FBQ1csT0FBTyxDQUFFLDJDQUE0QyxDQUFDLENBQUNRLEVBQUUsQ0FBRSxPQUFPLEVBQUUsWUFBVztJQUN2RmxCLFdBQVcsQ0FBQ21CLElBQUksQ0FBQyxDQUFDO0VBQ25CLENBQUUsQ0FBQztFQUVILE9BQU9uQixXQUFXO0FBQ25CLENBQUM7O0FBRUQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ08sSUFBTW9CLGFBQWEsR0FBQW5DLE9BQUEsQ0FBQW1DLGFBQUEsR0FBRyxTQUFoQkEsYUFBYUEsQ0FBYUMsS0FBSyxFQUFFQyxRQUFRLEVBQUc7RUFDeEQ7RUFDQSxJQUFLLE9BQU9DLE1BQU0sQ0FBQ0MsT0FBTyxLQUFLLFVBQVUsRUFBRztJQUMzQztFQUNEO0VBRUEsSUFBTUMsUUFBUSxHQUFHQyxNQUFNLENBQUVKLFFBQVEsRUFBRUQsS0FBTSxDQUFDO0VBRTFDSSxRQUFRLENBQUNFLElBQUksQ0FBRSxVQUFFQyxLQUFLLEVBQUVDLE9BQU8sRUFBTTtJQUFBLElBQUFDLHFCQUFBO0lBQ3BDLElBQU1DLEtBQUssR0FBR0wsTUFBTSxDQUFFRyxPQUFRLENBQUM7SUFFL0IsSUFBSyxXQUFXLEtBQUssT0FBT0UsS0FBSyxDQUFDQyxJQUFJLENBQUUsV0FBWSxDQUFDLEVBQUc7TUFDdkQ7SUFDRDtJQUVBLENBQUFGLHFCQUFBLEdBQUFHLE9BQU8sQ0FBQ0MsS0FBSyxDQUFDQyxPQUFPLENBQUNDLFFBQVEsY0FBQU4scUJBQUEsZUFBOUJBLHFCQUFBLENBQWdDTyxhQUFhLENBQUUsSUFBSyxDQUFDO0lBRXJETixLQUFLLENBQUNDLElBQUksQ0FBRSxXQUFXLEVBQUUsSUFBSVIsT0FBTyxDQUFFTyxLQUFLLENBQUUsQ0FBQyxDQUFFLEVBQUU7TUFDakRPLFVBQVUsRUFBRSxLQUFLO01BQ2pCQyxnQkFBZ0IsRUFBRSxJQUFJO01BQ3RCQyxXQUFXLEVBQUU7UUFDWkMsU0FBUyxFQUFFLEdBQUc7UUFDZEMsUUFBUSxFQUFFO01BQ1gsQ0FBQztNQUNEQyxjQUFjLFdBQWRBLGNBQWNBLENBQUEsRUFBRztRQUFBLElBQUFDLHNCQUFBO1FBQ2hCQyxHQUFHLENBQUNDLDRCQUE0QixDQUFFLElBQUssQ0FBQztRQUN4Q0QsR0FBRyxDQUFDRSx3QkFBd0IsQ0FBRSxJQUFJLENBQUNDLGNBQWMsQ0FBQ25CLE9BQVEsQ0FBQztRQUMzRCxDQUFBZSxzQkFBQSxHQUFBWCxPQUFPLENBQUNDLEtBQUssQ0FBQ0MsT0FBTyxDQUFDQyxRQUFRLGNBQUFRLHNCQUFBLGVBQTlCQSxzQkFBQSxDQUFnQ1AsYUFBYSxDQUFFLFVBQVcsQ0FBQztNQUM1RDtJQUNELENBQUUsQ0FBRSxDQUFDO0VBQ04sQ0FBRSxDQUFDO0FBQ0osQ0FBQyIsImlnbm9yZUxpc3QiOltdfQ==
},{}]},{},[1])