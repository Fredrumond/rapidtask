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
/******/ 	return __webpack_require__(__webpack_require__.s = 7);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/projetos/projeto-ver.js":
/*!**********************************************!*\
  !*** ./resources/js/projetos/projeto-ver.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $(".voltar-projeto").click(function () {
    console.log("oi");
    var id = $(this).data("id");
    window.location = '/admin/projeto/detalhe/' + id;
  });
  $('#form-atualiza-projeto').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    var dados = form.serialize();
    console.log(dados);
    alertify.set('notifier', 'position', 'top-right');

    if ($('#nome').val() == '') {
      alertify.warning('Preencha o nome!');
    }

    if ($('#sigla').val() == '') {
      alertify.warning('Preencha a sigla!');
    }

    if ($('#cliente_id').val() == '') {
      alertify.warning('Preencha o cliente!');
    }

    if ($('#titulo').val() != '' && $('#cliente_id').val() != '' && $('#sigla').val() != '') {
      $.ajax({
        url: ' /admin/projeto/editar',
        type: 'POST',
        dataType: 'json',
        data: dados
      }).done(function (response) {
        window.location = '/admin/projeto/detalhe/' + response.id;
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    }
  });
  $('.excluir-projeto').click(function (e) {
    e.preventDefault();
    var projetoId = $(this).data('id');
    alertify.confirm('Deseja realmente excluir o projeto?').set('onok', function (closeEvent) {
      $.ajax({
        url: ' /admin/projeto/excluir',
        type: 'GET',
        dataType: 'json',
        data: {
          'projetoId': projetoId
        }
      }).done(function (response) {
        window.location.replace("/admin/projetos");
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    });
  });
});

/***/ }),

/***/ 7:
/*!****************************************************!*\
  !*** multi ./resources/js/projetos/projeto-ver.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Users\FREDERICO\Desktop\Projetos\rapidtask\resources\js\projetos\projeto-ver.js */"./resources/js/projetos/projeto-ver.js");


/***/ })

/******/ });