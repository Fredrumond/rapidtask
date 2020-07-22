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
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/tarefas/tarefa-main.js":
/*!*********************************************!*\
  !*** ./resources/js/tarefas/tarefa-main.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $(".ver-detalhes-tarefa").click(function () {
    var id = $(this).data("id");
    window.location = '/admin/tarefa/ver/' + id;
  });

  function retornaComentarios() {
    var tarefa_id = $('#tarefaId').val();
    $.ajax({
      url: '/admin/tarefa-comentarios',
      type: 'GET',
      dataType: 'json',
      data: {
        'tarefa_id': tarefa_id
      }
    }).done(function (response) {
      var comentarios = "";
      $.each(response.comentarios, function (key, value) {
        comentarios += '<li class="feed-item"><div class="feed-item-list"><span class="date">' + value.nome + '   ' + value.data + '</span> <span class="activity-text">' + value.comentario + '</span><div class="acoes-comentario"><i class="fas fa-edit editar-comentario" data-id="' + value.id + '"></i><i class="fas fa-trash remover-comentario" data-id="' + value.id + '"></i></div></div></li>';
      });
      $('.timeline-comentarios').html(comentarios);
    }).fail(function (error) {
      console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
      console.log(error);
    });
  }

  retornaComentarios();

  function retornaHistorico() {
    var tarefa_id = $('#tarefaId').val();
    $.ajax({
      url: '/admin/tarefa-historico',
      type: 'GET',
      dataType: 'json',
      data: {
        'tarefa_id': tarefa_id
      }
    }).done(function (response) {
      var historico = "";
      $.each(response.historico, function (key, value) {
        historico += '<li class="feed-item"><div class="feed-item-list"><span class="date">' + value.nome + ' alterou ' + value.atividade + '   ' + value.data + '</span> </div></li>';
      });
      $('.timeline-historico').html(historico);
    }).fail(function (error) {
      console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
      console.log(error);
    });
  }

  retornaHistorico();
  $('.box-comentario').hide();
  $('.novo-comentario').click(function (event) {
    $('.box-comentario').show();
    $('.novo-comentario').hide();
  });
  $('.cancelar-comentario').click(function (event) {
    $('.box-comentario').hide();
    $('.novo-comentario').show();
  });
  $('#form-atualiza-tarefa').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    var dados = form.serialize();
    alertify.set('notifier', 'position', 'top-right');

    if ($('#titulo').val() == '') {
      alertify.warning('Preencha o titulo!');
    }

    if ($('#tipo_id').val() == '') {
      alertify.warning('Preencha o tipo!');
    }

    if ($('#situacao_id').val() == '') {
      alertify.warning('Preencha a situação!');
    }

    if ($('#prioridade_id').val() == '') {
      alertify.warning('Preencha a prioridade!');
    }

    if ($('#titulo').val() != '' && $('#tipo_id').val() != '' && $('#situacao_id').val() != '' && $('#prioridade_id').val() != '') {
      $.ajax({
        url: ' /admin/tarefa/editar',
        type: 'POST',
        dataType: 'json',
        data: dados
      }).done(function (response) {
        if (response.redireciona == true) {
          window.location = '/admin/tarefas';
        } else {
          window.location = '/admin/tarefa/ver/' + response.id;
        }
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    }
  });
  $('.arquivar-tarefa').click(function (e) {
    e.preventDefault();
    var tarefaId = $('#tarefa_id').val();
    alertify.confirm('Deseja realmente arquivar a tarefa?').set('onok', function (closeEvent) {
      $.ajax({
        url: ' /admin/tarefa/arquivar',
        type: 'GET',
        dataType: 'json',
        data: {
          'tarefaId': tarefaId
        }
      }).done(function (response) {
        if (response.status == '200') {
          window.location.replace("/admin/tarefas");
        }
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    });
  });
  $('#form-comentario').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    var dados = form.serialize();
    alertify.set('notifier', 'position', 'top-right');

    if ($("#comentario").val().trim().length < 1) {
      alertify.warning('Preencha o comentario!');
    }

    if ($("#comentario").val().trim().length > 1) {
      $.ajax({
        url: ' /admin/tarefa-comentario/salvar',
        type: 'POST',
        dataType: 'json',
        data: dados
      }).done(function (response) {
        if (response.status == '200') {
          var id = $('#tarefaId').val();
          $('.box-comentario').hide();
          $('.novo-comentario').show();
          $('#comentario').val('');
          retornaComentarios();
        }
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    }
  });
  $(document).on('click', '.editar-comentario', function () {
    var comentarioId = $(this).data("id");
    $.ajax({
      url: '/admin/tarefa-comentario/ver',
      type: 'GET',
      dataType: 'json',
      data: {
        'comentarioId': comentarioId
      }
    }).done(function (response) {
      $('#comentarioEditar').val(response.data.comentario);
      $('#comentarioId').val(response.data.id);
      $('#editarComentarioModal').modal('show');
    }).fail(function (error) {
      console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
      console.log(error);
    });
  });
  $(document).on('click', '.remover-comentario', function () {
    var comentarioId = $(this).data("id");
    alertify.confirm('Deseja realmente excluir o comentario?').set('onok', function (closeEvent) {
      $.ajax({
        url: ' /admin/tarefa-comentario/excluir',
        type: 'GET',
        dataType: 'json',
        data: {
          'comentarioId': comentarioId
        }
      }).done(function (response) {
        if (response.status == '200') {
          retornaComentarios();
        }
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    });
  });
  $('.cancelar-comentario-editar').click(function (event) {
    $('#editarComentarioModal').modal('hide');
  });
  $('#form-comentario-editar').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    var dados = form.serialize();
    alertify.set('notifier', 'position', 'top-right');

    if ($("#comentarioEditar").val().trim().length < 1) {
      alertify.warning('Preencha o comentario!');
    }

    if ($("#comentarioEditar").val().trim().length > 1) {
      $.ajax({
        url: ' /admin/tarefa-comentario/editar',
        type: 'POST',
        dataType: 'json',
        data: dados
      }).done(function (response) {
        if (response.status == '200') {
          $('#editarComentarioModal').modal('hide');
          retornaComentarios();
        }
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    }
  });
  $('#form-tarefa').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    var dados = form.serialize();
    alertify.set('notifier', 'position', 'top-right');

    if ($('#titulo').val() == '') {
      alertify.warning('Preencha o titulo!');
    }

    if ($('#tipo_id').val() == '') {
      alertify.warning('Preencha o tipo!');
    }

    if ($('#situacao_id').val() == '') {
      alertify.warning('Preencha a situação!');
    }

    if ($('#prioridade_id').val() == '') {
      alertify.warning('Preencha a prioridade!');
    }

    if ($('#titulo').val() != '' && $('#tipo_id').val() != '' && $('#situacao_id').val() != '' && $('#prioridade_id').val() != '') {
      $.ajax({
        url: '/admin/tarefa/salvar',
        type: 'POST',
        dataType: 'json',
        data: dados
      }).done(function (response) {
        window.location.replace("/admin/tarefas");
      }).fail(function () {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    }
  });
  $('.excluir-tarefa').click(function (e) {
    e.preventDefault();
    var tarefaId = $(this).data('id');
    alertify.confirm('Deseja realmente excluir a tarefa?').set('onok', function (closeEvent) {
      $.ajax({
        url: ' /admin/tarefa/excluir',
        type: 'GET',
        dataType: 'json',
        data: {
          'tarefaId': tarefaId
        }
      }).done(function (response) {
        if (response.status == '200') {
          window.location.replace("/admin/tarefa/arquivadas");
        }
      }).fail(function (error) {
        console.log("error");
      });
    });
  });
  $('.restaurar-tarefa').click(function (e) {
    e.preventDefault();
    var tarefaId = $(this).data('id');
    alertify.confirm('Deseja realmente restaurar a tarefa?').set('onok', function (closeEvent) {
      $.ajax({
        url: ' /admin/tarefa/recuperar',
        type: 'GET',
        dataType: 'json',
        data: {
          'tarefaId': tarefaId
        }
      }).done(function (response) {
        if (response.status == '200') {
          window.location.replace("/admin/tarefa/arquivadas");
        }
      }).fail(function (error) {
        console.log("error");
      });
    });
  });
});

/***/ }),

/***/ 1:
/*!***************************************************!*\
  !*** multi ./resources/js/tarefas/tarefa-main.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/fredericodrumond/Documents/Projetos/rapidtask/resources/js/tarefas/tarefa-main.js */"./resources/js/tarefas/tarefa-main.js");


/***/ })

/******/ });