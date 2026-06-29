$(function () {

  'use strict';

  var console = window.console || { log: function () {} };
  var $image = $('#image');
  var $download = $('#download');
  var $dataX = $('#dataX');
  var $dataY = $('#dataY');
  var $dataHeight = $('#dataHeight');
  var $dataWidth = $('#dataWidth');
  var $dataRotate = $('#dataRotate');
  var $dataScaleX = $('#dataScaleX');
  var $dataScaleY = $('#dataScaleY');
  var $dataBrush = $('#dataBrush');
  var $dataBrushPoints = $('#dataBrushPoints');
  var $dataBrushColor = $('#dataBrushColor');
  var $dataBrushSize = $('#dataBrushSize');
  var $brushStatus = $('#brushStatus');
  var $imgContainer = $('.img-container');
  var $brushCanvas = $('#brushCanvas');
  var $brushSize = $('#brushSize');
  var $brushSizeText = $('#brushSizeText');
  var $brushColor = $('#brushColor');
  var $toggleBrush = $('#toggleBrush');
  var brushCanvas = $brushCanvas[0];
  var brushCtx = brushCanvas ? brushCanvas.getContext('2d') : null;
  var brushData = {
    enabled: false,
    size: 24,
    color: '#ff0000',
    points: []
  };
  var isDrawing = false;
  var options = {
        aspectRatio: NaN,
        autoCropArea: 1,
        preview: '.img-preview',
        crop: function (e) {
          $dataX.val(Math.round(e.x));
          $dataY.val(Math.round(e.y));
          $dataHeight.val(Math.round(e.height));
          $dataWidth.val(Math.round(e.width));
          $dataRotate.val(e.rotate);
          $dataScaleX.val(e.scaleX);
          $dataScaleY.val(e.scaleY);
        }
      };

  function syncBrushData() {
    if ($dataBrush.length) {
      $dataBrush.val(JSON.stringify({
        size: brushData.size,
        color: brushData.color,
        points: brushData.points
      }));
    }
    if ($dataBrushPoints.length) {
      $dataBrushPoints.val(encodeURIComponent(JSON.stringify(brushData.points)));
    }
    if ($dataBrushColor.length) {
      $dataBrushColor.val(brushData.color);
    }
    if ($dataBrushSize.length) {
      $dataBrushSize.val(brushData.size);
    }
  }

  function getImageData() {
    if (!$image.data('cropper')) {
      return null;
    }
    return $image.cropper('getImageData');
  }

  function getDisplayRect() {
    var imageData = getImageData();
    var imgEl;
    var imgRect;
    var canvasRect;
    if (!imageData || !brushCanvas) {
      return null;
    }
    imgEl = $imgContainer.find('.cropper-canvas img').get(0);
    if (!imgEl) {
      return null;
    }
    imgRect = imgEl.getBoundingClientRect();
    canvasRect = brushCanvas.getBoundingClientRect();
    return {
      left: imgRect.left - canvasRect.left,
      top: imgRect.top - canvasRect.top,
      width: imgRect.width,
      height: imgRect.height,
      naturalWidth: imageData.naturalWidth,
      naturalHeight: imageData.naturalHeight
    };
  }

  function ensureCanvasInCropper() {
    var $cropperContainer = $imgContainer.find('.cropper-container');
    if (!$cropperContainer.length || !$brushCanvas.length) {
      return;
    }
    if (!$brushCanvas.parent().is($cropperContainer)) {
      $brushCanvas.appendTo($cropperContainer);
      $brushCanvas.css({
        position: 'absolute',
        left: 0,
        top: 0,
        'z-index': 20
      });
    }
  }

  function updateCanvasSize() {
    var $cropperContainer = $imgContainer.find('.cropper-container');
    if (!brushCanvas || !$imgContainer.length || !$cropperContainer.length) {
      return;
    }
    var cropperWidth = Math.round($cropperContainer.innerWidth());
    var cropperHeight = Math.round($cropperContainer.innerHeight());
    brushCanvas.width = cropperWidth;
    brushCanvas.height = cropperHeight;
    $brushCanvas.css({
      left: '0px',
      top: '0px',
      width: cropperWidth + 'px',
      height: cropperHeight + 'px'
    });
  }

  function renderBrushOverlay() {
    var displayRect;
    if (!brushCtx) {
      return;
    }
    ensureCanvasInCropper();
    updateCanvasSize();
    displayRect = getDisplayRect();
    if (!displayRect || !displayRect.width || !displayRect.height) {
      return;
    }
    brushCtx.clearRect(0, 0, brushCanvas.width, brushCanvas.height);
    if (!brushData.points.length) {
      syncBrushData();
      renderBrushStatus();
      return;
    }

    var scaleX = displayRect.width / displayRect.naturalWidth;
    var scaleY = displayRect.height / displayRect.naturalHeight;
    $.each(brushData.points, function (i, point) {
      var drawX = displayRect.left + point.x * scaleX;
      var drawY = displayRect.top + point.y * scaleY;
      var drawR = Math.max(3, point.r * ((scaleX + scaleY) / 2));
      brushCtx.fillStyle = point.c || brushData.color;
      brushCtx.globalAlpha = 0.45;
      brushCtx.beginPath();
      brushCtx.arc(drawX, drawY, drawR, 0, Math.PI * 2, false);
      brushCtx.fill();
    });
    brushCtx.globalAlpha = 1;

    syncBrushData();
    renderBrushStatus();
  }

  function renderBrushStatus() {
    if (!$brushStatus.length) {
      return;
    }
    if (!brushData.enabled) {
      $brushStatus.text('未开启画笔');
      return;
    }
    $brushStatus.text('已绘制 ' + brushData.points.length + ' 笔，按住鼠标左键在图上涂抹');
  }

  function toNaturalPoint(e) {
    var displayRect = getDisplayRect();
    var rect;
    if (!displayRect || !displayRect.width || !displayRect.height || !brushCanvas) {
      return null;
    }
    rect = brushCanvas.getBoundingClientRect();
    var relX = e.clientX - rect.left;
    var relY = e.clientY - rect.top;
    if (relX < displayRect.left || relY < displayRect.top || relX > displayRect.left + displayRect.width || relY > displayRect.top + displayRect.height) {
      return null;
    }
    var naturalX = Math.round((relX - displayRect.left) * displayRect.naturalWidth / displayRect.width);
    var naturalY = Math.round((relY - displayRect.top) * displayRect.naturalHeight / displayRect.height);
    return {
      x: Math.max(0, Math.min(displayRect.naturalWidth - 1, naturalX)),
      y: Math.max(0, Math.min(displayRect.naturalHeight - 1, naturalY))
    };
  }

  function addBrushPoint(e) {
    var point = toNaturalPoint(e);
    var prev;
    var dx;
    var dy;
    if (!point) {
      return;
    }
    point.r = Math.max(4, Math.round(brushData.size / 2));
    point.c = brushData.color;
    prev = brushData.points.length ? brushData.points[brushData.points.length - 1] : null;
    if (prev) {
      dx = point.x - prev.x;
      dy = point.y - prev.y;
      if ((dx * dx + dy * dy) < (point.r * point.r) / 6) {
        return;
      }
    }
    brushData.points.push(point);
    renderBrushOverlay();
  }

  function setBrushMode(enabled) {
    brushData.enabled = !!enabled;
    if (brushData.enabled) {
      $brushCanvas.show().css('cursor', 'crosshair');
      $toggleBrush.text('关闭画笔').removeClass('btn-warning').addClass('btn-success');
    } else {
      isDrawing = false;
      $brushCanvas.hide();
      $toggleBrush.text('开启画笔').removeClass('btn-success').addClass('btn-warning');
    }
    renderBrushOverlay();
  }

  // Tooltip
  $('[data-toggle="tooltip"]').tooltip();

  // Cropper
  $image.on({
    'build.cropper': function (e) {
      console.log(e.type);
    },
    'built.cropper': function (e) {
      console.log(e.type);
      ensureCanvasInCropper();
      renderBrushOverlay();
    },
    'cropstart.cropper': function (e) {
      console.log(e.type, e.action);
    },
    'cropmove.cropper': function (e) {
      console.log(e.type, e.action);
    },
    'cropend.cropper': function (e) {
      console.log(e.type, e.action);
    },
    'crop.cropper': function (e) {
      console.log(e.type, e.x, e.y, e.width, e.height, e.rotate, e.scaleX, e.scaleY);
      renderBrushOverlay();
    },
    'zoom.cropper': function (e) {
      console.log(e.type, e.ratio);
      setTimeout(renderBrushOverlay, 0);
    }
  }).cropper(options);


  // Buttons
  if (!$.isFunction(document.createElement('canvas').getContext)) {
    $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
  }

  if (typeof document.createElement('cropper').style.transition === 'undefined') {
    $('button[data-method="rotate"]').prop('disabled', true);
    $('button[data-method="scale"]').prop('disabled', true);
  }

  // Options
  $('.docs-toggles').on('change', 'input', function () {
    var $this = $(this);
    var name = $this.attr('name');
    var type = $this.prop('type');
    var cropBoxData;
    var canvasData;

    if (!$image.data('cropper')) {
      return;
    }

    if (type === 'checkbox') {
      options[name] = $this.prop('checked');
      cropBoxData = $image.cropper('getCropBoxData');
      canvasData = $image.cropper('getCanvasData');

      options.built = function () {
        $image.cropper('setCropBoxData', cropBoxData);
        $image.cropper('setCanvasData', canvasData);
      };
    } else if (type === 'radio') {
      options[name] = $this.val();
    }

    $image.cropper('destroy').cropper(options);
  });

  // Methods
  $('.docs-buttons').on('click', '[data-method]', function () {
    var $this = $(this);
    var data = $this.data();
    var $target;
    var result;

    if ($this.prop('disabled') || $this.hasClass('disabled')) {
      return;
    }

    if ($image.data('cropper') && data.method) {
      data = $.extend({}, data); // Clone a new one

      if (typeof data.target !== 'undefined') {
        $target = $(data.target);

        if (typeof data.option === 'undefined') {
          try {
            data.option = JSON.parse($target.val());
          } catch (e) {
            console.log(e.message);
          }
        }
      }

      if (data.method === 'rotate') {
        $image.cropper('clear');
      }

      result = $image.cropper(data.method, data.option, data.secondOption);

      if (data.method === 'rotate') {
        $image.cropper('crop');
      }

      switch (data.method) {
        case 'scaleX':
        case 'scaleY':
          $(this).data('option', -data.option);
          break;

        case 'getCroppedCanvas':
          if (result) {

            // Bootstrap's Modal
            $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);

            if (!$download.hasClass('disabled')) {
              $download.attr('href', result.toDataURL('image/jpeg'));
            }
          }

          break;
      }

      if ($.isPlainObject(result) && $target) {
        try {
          $target.val(JSON.stringify(result));
        } catch (e) {
          console.log(e.message);
        }
      }

    }
  });

  $toggleBrush.on('click', function () {
    setBrushMode(!brushData.enabled);
  });

  $('#clearBrush').on('click', function () {
    brushData.points = [];
    renderBrushOverlay();
  });

  $brushSize.on('input change', function () {
    brushData.size = parseInt($(this).val(), 10) || 24;
    $brushSizeText.text(brushData.size);
    syncBrushData();
    renderBrushOverlay();
  });

  $brushColor.on('change input', function () {
    brushData.color = $(this).val() || '#ff0000';
    syncBrushData();
    renderBrushOverlay();
  });

  $brushCanvas.on('mousedown', function (e) {
    if (!brushData.enabled || e.which !== 1) {
      return;
    }
    isDrawing = true;
    addBrushPoint(e);
    e.preventDefault();
  }).on('mousemove', function (e) {
    if (!brushData.enabled || !isDrawing) {
      return;
    }
    addBrushPoint(e);
    e.preventDefault();
  }).on('mouseup mouseleave', function () {
    isDrawing = false;
  });

  $(window).on('resize', function () {
    renderBrushOverlay();
  });

  updateCanvasSize();
  renderBrushOverlay();
  renderBrushStatus();

  // Keyboard
  $(document.body).on('keydown', function (e) {

    if (!$image.data('cropper') || this.scrollTop > 300) {
      return;
    }

    switch (e.which) {
      case 37:
        e.preventDefault();
        $image.cropper('move', -1, 0);
        break;

      case 38:
        e.preventDefault();
        $image.cropper('move', 0, -1);
        break;

      case 39:
        e.preventDefault();
        $image.cropper('move', 1, 0);
        break;

      case 40:
        e.preventDefault();
        $image.cropper('move', 0, 1);
        break;
    }

  });

  // Import image
  var $inputImage = $('#inputImage');
  var URL = window.URL || window.webkitURL;
  var blobURL;

  if (URL) {
    $inputImage.change(function () {
      var files = this.files;
      var file;

      if (!$image.data('cropper')) {
        return;
      }

      if (files && files.length) {
        file = files[0];

        if (/^image\/\w+$/.test(file.type)) {
          blobURL = URL.createObjectURL(file);
          $image.one('built.cropper', function () {

            // Revoke when load complete
            URL.revokeObjectURL(blobURL);
          }).cropper('reset').cropper('replace', blobURL);
          $inputImage.val('');
        } else {
          window.alert('Please choose an image file.');
        }
      }
    });
  } else {
    $inputImage.prop('disabled', true).parent().addClass('disabled');
  }
});