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
/******/ 	return __webpack_require__(__webpack_require__.s = 8);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/projetos/projeto-detalhes.js":
/*!***************************************************!*\
  !*** ./resources/js/projetos/projeto-detalhes.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $(".ver-detalhes-tarefa").click(function () {
    console.log("oi");
    var id = $(this).data("id");
    window.location = '/admin/tarefa/ver/' + id;
  });
  $(".config-projeto").click(function () {
    var projetoId = $(this).data('id');
    window.location = '/admin/projeto/ver/' + projetoId;
  });
  $('.adicionar-arquivo-projeto').click(function (event) {
    $('#adicionarArquivoProjetoModal').modal('show');
  });
  $('.box-anotacao').hide();
  $('.nova-anotacao').click(function (event) {
    $('.box-anotacao').show();
    $('.novo-anotacao').hide();
  });
  $('.cancelar-anotacao').click(function (event) {
    $('.box-anotacao').hide();
    $('.novo-anotacao').show();
  });

  function retornaAnotacoes() {
    var projeto_id = $('#projeto_id').val();
    $.ajax({
      url: '/admin/projeto-anotacoes',
      type: 'GET',
      dataType: 'json',
      data: {
        'projeto_id': projeto_id
      }
    }).done(function (response) {
      var anotacoes = "";
      $.each(response.anotacoes, function (key, value) {
        anotacoes += '<li class="feed-item"><div class="feed-item-list"><span class="date">' + value.nome + '   ' + value.data + '</span> <span class="activity-text">' + value.anotacao + '</span><div class="acoes-anotacao"><i class="fas fa-edit editar-anotacao" data-id="' + value.id + '"></i><i class="fas fa-trash remover-anotacao" data-id="' + value.id + '"></i></div></div></li>';
      });
      $('.timeline-anotacoes').html(anotacoes);
    }).fail(function (error) {
      console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
      console.log(error);
    });
  }

  retornaAnotacoes();
  $('#form-arquivo-projeto').submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    alertify.set('notifier', 'position', 'top-right');

    if ($('#nome').val() == '') {
      alertify.warning('Preencha o nome!');
    }

    if ($('#arquivo').val() == '') {
      alertify.warning('Preencha o arquivo!');
    }

    if ($("#descricao").val().trim().length < 1) {
      alertify.warning('Preencha a descrição');
    }

    if ($('#nome').val() != '' && $('#arquivo').val() != '' && $('#descricao').val() != '') {
      $.ajax({
        url: ' /admin/projeto/arquivo/novo',
        type: 'POST',
        dataType: 'json',
        data: formData,
        cache: false,
        contentType: false,
        processData: false
      }).done(function (response) {
        alertify.success(response.msg);
        $('#adicionarArquivoProjetoModal').modal('hide');
        $('#nome').val('');
        $('#arquivo').val('');
        $('#descricao').val('');
        retornaArquivos();
      }).fail(function (error) {
        alertify.error(error.responseJSON.msg);
      });
    }
  });

  function retornaArquivos() {
    var projeto_id = $('#projeto_id').val();
    $.ajax({
      url: '/admin/projeto/arquivos',
      type: 'GET',
      dataType: 'json',
      data: {
        'projeto_id': projeto_id
      }
    }).done(function (response) {
      var arquivos = "";
      $.each(response.arquivos, function (key, value) {
        arquivos += '<tr class="text-center"><td>' + value.nome + '</td><td>' + value.descricao + '</td><td>' + value.created_at + '</td><td><a class="btn btn-info" href="/projetos/arquivos/' + value.src + '" target="_blank">Visualizar</a><button class="btn btn-danger excluir-arquivo" data-id="' + value.id + '">Excluir</button></td></tr>';
      });
      $('.tabela-arquivos-lista').html(arquivos);
    }).fail(function (error) {
      console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
      console.log(error);
    });
  }

  retornaArquivos();
  $(document).on('click', '.excluir-arquivo', function () {
    var arquivo_id = $(this).data("id");
    alertify.confirm('Deseja realmente excluir o arquivo?').set('onok', function (closeEvent) {
      $.ajax({
        url: ' /admin/projeto/arquivo/excluir',
        type: 'GET',
        dataType: 'json',
        data: {
          'arquivo_id': arquivo_id
        }
      }).done(function (response) {
        if (response.status == '200') {
          retornaArquivos();
        }
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    });
  });
  $('#form-anotacao').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    var dados = form.serialize();
    console.log(dados);
    alertify.set('notifier', 'position', 'top-right');

    if ($("#anotacao").val().trim().length < 1) {
      alertify.warning('Preencha a anotação!');
    }

    if ($("#anotacao").val().trim().length > 1) {
      $.ajax({
        url: ' /admin/projeto-anotacao/salvar',
        type: 'POST',
        dataType: 'json',
        data: dados
      }).done(function (response) {
        if (response.status == '200') {
          var id = $('#anotacao_id').val();
          $('.box-anotacao').hide();
          $('.nova-anotacao').show();
          $('#comentario').val('');
          retornaAnotacoes();
        }
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    }
  });
  $(document).on('click', '.editar-anotacao', function () {
    var anotacao_id = $(this).data("id");
    $.ajax({
      url: '/admin/projeto-anotacao/ver',
      type: 'GET',
      dataType: 'json',
      data: {
        'anotacao_id': anotacao_id
      }
    }).done(function (response) {
      $('#anotacaoEditar').val(response.data.anotacao);
      $('#anotacao_id').val(response.data.id);
      $('#editarAnotacaoModal').modal('show');
    }).fail(function (error) {
      console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
      console.log(error);
    });
  });
  $(document).on('click', '.remover-anotacao', function () {
    var anotacao_id = $(this).data("id");
    alertify.confirm('Deseja realmente excluir a anotação?').set('onok', function (closeEvent) {
      $.ajax({
        url: ' /admin/projeto-anotacao/excluir',
        type: 'GET',
        dataType: 'json',
        data: {
          'anotacao_id': anotacao_id
        }
      }).done(function (response) {
        if (response.status == '200') {
          retornaAnotacoes();
        }
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    });
  });
  $('.cancelar-anotacao-editar').click(function (event) {
    $('#editarAnotacaoModal').modal('hide');
  });
  $('#form-anotacao-editar').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    var dados = form.serialize();
    console.log(dados);
    alertify.set('notifier', 'position', 'top-right');

    if ($("#anotacaoEditar").val().trim().length < 1) {
      alertify.warning('Preencha a anotação!');
    }

    if ($("#anotacaoEditar").val().trim().length > 1) {
      $.ajax({
        url: ' /admin/projeto-anotacao/editar',
        type: 'POST',
        dataType: 'json',
        data: dados
      }).done(function (response) {
        if (response.status == '200') {
          $('#editarAnotacaoModal').modal('hide');
          retornaAnotacoes();
        }
      }).fail(function (error) {
        console.log('Foi encontrado um erro durante a execução. Entre em contato com a equipe de desenvolvimento!');
        console.log(error);
      });
    }
  });
});

/***/ }),

/***/ 8:
/*!*********************************************************!*\
  !*** multi ./resources/js/projetos/projeto-detalhes.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/fredericodrumond/Documents/Projetos/rapidtask/resources/js/projetos/projeto-detalhes.js */"./resources/js/projetos/projeto-detalhes.js");


/***/ })

/******/ });