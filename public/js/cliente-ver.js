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
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 5);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/clientes/cliente-ver.js":
/*!**********************************************!*\
  !*** ./resources/js/clientes/cliente-ver.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  console.log("TA ENTRANDO NO CV?"); //VER DETALHES DO PROJETO

  $(".ver-detalhes-projeto").click(function () {
    var id = $(this).data("id");
    window.location = '/admin/projeto/detalhe/' + id;
  }); //ATUALIZAR DADOS DO CLIENTE

  $('#form-atualiza-cliente').submit(function (e) {
    e.preventDefault();
    console.log("VEIO NO SUBMIT");
    var form = $(this);
    var dados = form.serialize();
    alertify.set('notifier', 'position', 'top-right');

    if ($('#nome').val() == '') {
      alertify.warning('Preencha o nome!');
    }

    if ($('#nome').val() != '') {
      $.ajax({
        url: ' /admin/cliente/editar',
        type: 'POST',
        dataType: 'json',
        data: dados
      }).done(function (response) {
        window.location.replace("/admin/clientes");
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    }
  }); //EXCLUIR CLIENTE

  $('.excluir-cliente').click(function (e) {
    e.preventDefault();
    var clienteId = $(this).data('id');
    alertify.confirm('Deseja realmente excluir o cliente?').set('onok', function (closeEvent) {
      $.ajax({
        url: ' /admin/cliente/excluir',
        type: 'GET',
        dataType: 'json',
        data: {
          'clienteId': clienteId
        }
      }).done(function (response) {
        window.location.replace("/admin/clientes");
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    });
  });
});

/***/ }),

/***/ 5:
/*!****************************************************!*\
  !*** multi ./resources/js/clientes/cliente-ver.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/fredericodrumond/Documents/Projetos/rapidtask/resources/js/clientes/cliente-ver.js */"./resources/js/clientes/cliente-ver.js");


/***/ })

/******/ });