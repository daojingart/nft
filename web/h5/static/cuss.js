(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.lianlianpay = factory());
})(this, (function () { 'use strict';

  function ownKeys(object, enumerableOnly) {
    var keys = Object.keys(object);

    if (Object.getOwnPropertySymbols) {
      var symbols = Object.getOwnPropertySymbols(object);
      enumerableOnly && (symbols = symbols.filter(function (sym) {
        return Object.getOwnPropertyDescriptor(object, sym).enumerable;
      })), keys.push.apply(keys, symbols);
    }

    return keys;
  }

  function _objectSpread2(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = null != arguments[i] ? arguments[i] : {};
      i % 2 ? ownKeys(Object(source), !0).forEach(function (key) {
        _defineProperty(target, key, source[key]);
      }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) {
        Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
      });
    }

    return target;
  }

  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }

    return obj;
  }

  var baseHost = 'https://accpgw.lianlianpay.com'; // const baseHost = 'http://192.168.130.61:8088'
  // const baseHost = 'http://localhost:3000'
  // const businessURL = baseHost

  var businessURL = "".concat(baseHost, "/mpay-accp-password-web/");

  var createIframeId = function createIframeId() {
    return Date.now() + Math.random().toString(36).slice(-6);
  };

  var lianlianpay = {
    version: '1.0.0',
    iframeClassName: 'lianlianpay-password-iframe',
    iframe: null,
    invokePasswordControl: function invokePasswordControl() {
		console.log(8888888888)
      var _this = this;

      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var callback = arguments.length > 1 ? arguments[1] : undefined;
      console.log('call invokePasswordControl', options);

      if (!options.oidPartner) {
        throw new Error('璇疯緭鍏ュ晢鎴峰彿');
      }

      if (!options.passwordScene) {
        throw new Error('璇疯緭鍏ュ瘑鐮佷娇鐢ㄥ満鏅�');
      }

      if (!options.passwordElementToken) {
        throw new Error('璇疯緭鍏� Token');
      } // 鍒涘缓 iframe 鏃讹紝闇€瑕佸垹闄ょ幇瀛樼殑鎵€鏈� iframe 绐楀彛


      this.closeIframe();
      var iframe = document.createElement('iframe');
      iframe.className = this.iframeClassName;
      iframe.style.position = 'fixed';
      iframe.style.width = '100%';
      iframe.style.height = '100%';
      iframe.style.top = '0';
      iframe.style.left = '0';
      iframe.style.border = 'none';
      iframe.style.outline = 'none';
      iframe.src = "".concat(businessURL);
      iframe.id = createIframeId();
      document.body.appendChild(iframe);
      this.iframe = iframe;

      this.iframe.onload = function () {
        _this.iframe.contentWindow.postMessage(_objectSpread2({
          uuid: 'LIANLIAN_ONLOAD',
          iframeId: iframe.id
        }, options), baseHost);
      };

      window.addEventListener('message', function (event) {
        var data = event.data;

        switch (data.option) {
          case 'CLOSE':
            _this.closeIframe(data.id);

            break;

          case 'MESSAGE':
            console.log('Get Message', data.result);
            typeof callback === 'function' && callback({
              code: '0000',
              message: '鑾峰彇瀵嗙爜鎴愬姛',
              data: data.result
            });
            break;
        }
      });
    },

    /**
     * 鍏抽棴 iframe锛屼紶 id 鍏抽棴褰撳墠锛屽惁鍒欏叧闂墍鏈�
     * @param {String} id 褰撳墠 iframe 鐨� ID
     */
    closeIframe: function closeIframe(id) {
      if (!id) {
        // 鍒犻櫎鎵€鏈�
        var iframes = document.getElementsByClassName(this.iframeClassName);

        for (var i = 0; i < iframes.length; i++) {
          document.body.removeChild(iframes[i]);
        }

        return;
      }

      var iframe = document.getElementById(id);
      document.body.removeChild(iframe);
    }
  };

  return lianlianpay;

}));