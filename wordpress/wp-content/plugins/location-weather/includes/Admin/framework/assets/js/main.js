; (function ($, window, document, undefined) {
  'use strict';

  //
  // Constants
  //
  var SPLW = SPLW || {};

  SPLW.funcs = {};

  SPLW.vars = {
    onloaded: false,
    $body: $('body'),
    $window: $(window),
    $document: $(document),
    $form_warning: null,
    is_confirm: false,
    form_modified: false,
    code_themes: [],
    is_rtl: $('body').hasClass('rtl'),
  };

  //
  // Helper Functions
  //
  SPLW.helper = {

    //
    // Generate UID
    //
    uid: function (prefix) {
      return (prefix || '') + Math.random().toString(36).substr(2, 9);
    },

    // Quote regular expression characters
    //
    preg_quote: function (str) {
      return (str + '').replace(/(\[|\])/g, "\\$1");
    },

    //
    // Reneme input names
    //
    name_nested_replace: function ($selector, field_id) {

      var checks = [];
      var regex = new RegExp(SPLW.helper.preg_quote(field_id + '[\\d+]'), 'g');

      $selector.find(':radio').each(function () {
        if (this.checked || this.orginal_checked) {
          this.orginal_checked = true;
        }
      });

      $selector.each(function (index) {
        $(this).find(':input').each(function () {
          this.name = this.name.replace(regex, field_id + '[' + index + ']');
          if (this.orginal_checked) {
            this.checked = true;
          }
        });
      });

    },

    //
    // Debounce
    //
    debounce: function (callback, threshold, immediate) {
      var timeout;
      return function () {
        var context = this, args = arguments;
        var later = function () {
          timeout = null;
          if (!immediate) {
            callback.apply(context, args);
          }
        };
        var callNow = (immediate && !timeout);
        clearTimeout(timeout);
        timeout = setTimeout(later, threshold);
        if (callNow) {
          callback.apply(context, args);
        }
      };
    },
    //
    // Get a cookie
    //
    get_cookie: function (name) {

      var e, b, cookie = document.cookie, p = name + '=';

      if (!cookie) {
        return;
      }

      b = cookie.indexOf('; ' + p);

      if (b === -1) {
        b = cookie.indexOf(p);

        if (b !== 0) {
          return null;
        }
      } else {
        b += 2;
      }

      e = cookie.indexOf(';', b);

      if (e === -1) {
        e = cookie.length;
      }

      return decodeURIComponent(cookie.substring(b + p.length, e));

    },

    //
    // Set a cookie
    //
    set_cookie: function (name, value, expires, path, domain, secure) {

      var d = new Date();

      if (typeof (expires) === 'object' && expires.toGMTString) {
        expires = expires.toGMTString();
      } else if (parseInt(expires, 10)) {
        d.setTime(d.getTime() + (parseInt(expires, 10) * 1000));
        expires = d.toGMTString();
      } else {
        expires = '';
      }

      document.cookie = name + '=' + encodeURIComponent(value) +
        (expires ? '; expires=' + expires : '') +
        (path ? '; path=' + path : '') +
        (domain ? '; domain=' + domain : '') +
        (secure ? '; secure' : '');

    },

    //
    // Remove a cookie
    //
    remove_cookie: function (name, path, domain, secure) {
      SPLW.helper.set_cookie(name, '', -1000, path, domain, secure);
    },

  };

  //
  // Custom clone for textarea and select clone() bug
  //
  $.fn.splwt_clone = function () {

    var base = $.fn.clone.apply(this, arguments),
      clone = this.find('select').add(this.filter('select')),
      cloned = base.find('select').add(base.filter('select'));

    for (var i = 0; i < clone.length; ++i) {
      for (var j = 0; j < clone[i].options.length; ++j) {

        if (clone[i].options[j].selected === true) {
          cloned[i].options[j].selected = true;
        }

      }
    }

    this.find(':radio').each(function () {
      this.orginal_checked = this.checked;
    });

    return base;

  };

  //
  // Expand All Options
  //
  $.fn.splwt_expand_all = function () {
    return this.each(function () {
      $(this).on('click', function (e) {

        e.preventDefault();
        $('.splwt-lite-wrapper').toggleClass('splwt-lite-show-all');
        $('.splwt-lite-section').splwt_reload_script();
        $(this).find('.fa').toggleClass('fa-indent').toggleClass('fa-outdent');

      });
    });
  };

  //
  // Options Navigation
  //
  $.fn.splwt_nav_options = function () {
    return this.each(function () {

      var $nav = $(this),
        $links = $nav.find('a'),
        $last;

      $(window).on('hashchange splwt-lite.hashchange', function () {

        var hash = window.location.hash.replace('#tab=', '');
        var slug = hash ? hash : $links.first().attr('href').replace('#tab=', '');
        var $link = $('[data-tab-id="' + slug + '"]');

        if ($link.length) {

          $link.closest('.splwt-lite-tab-item').addClass('splwt-lite-tab-expanded').siblings().removeClass('splwt-lite-tab-expanded');

          if ($link.next().is('ul')) {

            $link = $link.next().find('li').first().find('a');
            slug = $link.data('tab-id');

          }

          $links.removeClass('splwt-lite-active');
          $link.addClass('splwt-lite-active');

          if ($last) {
            $last.hide();
          }

          var $section = $('[data-section-id="' + slug + '"]');

          $section.show();
          $section.splwt_reload_script();

          $('.splwt-lite-section-id').val($section.index());

          $last = $section;

        }

      }).trigger('splwt-lite.hashchange');

    });
  };

  //
  // MetaBox Tabs.
  //
  $.fn.splwt_nav_metabox = function () {
    return this.each(function () {

      var $nav = $(this),
        $links = $nav.find('a'),
        unique_id = $nav.data('unique'),
        post_id = $('#post_ID').val() || 'global',
        $last_section,
        $last_link;

      $links.on('click', function (e) {

        e.preventDefault();

        var $link = $(this),
          section_id = $link.data('section');

        if ($last_link !== undefined) {
          $last_link.removeClass('splwt-lite-active');
        }

        if ($last_section !== undefined) {
          $last_section.hide();
        }

        $link.addClass('splwt-lite-active');

        var $section = $('#splwt-section-' + section_id);
        $section.css({ display: 'block' });
        $section.splwt_reload_script();

        SPLW.helper.set_cookie('splwt-last-metabox-tab-' + post_id + '-' + unique_id, section_id);

        $last_section = $section;
        $last_link = $link;

      });

      var get_cookie = SPLW.helper.get_cookie('splwt-last-metabox-tab-' + post_id + '-' + unique_id);

      if (get_cookie) {
        $nav.find('a[data-section="' + get_cookie + '"]').trigger('click');
      } else {
        $links.first('a').trigger('click');
      }

    });
  };
  // //
  // // Metabox Tabs
  // //
  // $.fn.splwt_nav_metabox = function() {
  //   return this.each( function() {

  //     var $nav      = $(this),
  //         $links    = $nav.find('a'),
  //         $sections = $nav.parent().find('.splwt-lite-section'),
  //         $last;

  //     $links.each( function( index ) {

  //       $(this).on('click', function( e ) {

  //         e.preventDefault();

  //         var $link = $(this);

  //         $links.removeClass('splwt-lite-active');
  //         $link.addClass('splwt-lite-active');

  //         if ( $last !== undefined ) {
  //           $last.hide();
  //         }

  //         var $section = $sections.eq(index);

  //         $section.show();
  //         $section.splwt_reload_script();

  //         $last = $section;

  //       });

  //     });

  //     $links.first().trigger('click');

  //   });
  // };

  //
  // Metabox Page Templates Listener
  //
  $.fn.splwt_page_templates = function () {
    if (this.length) {

      $(document).on('change', '.editor-page-attributes__template select, #page_template', function () {

        var maybe_value = $(this).val() || 'default';

        $('.splwt-lite-page-templates').removeClass('splwt-lite-metabox-show').addClass('splwt-lite-metabox-hide');
        $('.splwt-lite-page-' + maybe_value.toLowerCase().replace(/[^a-zA-Z0-9]+/g, '-')).removeClass('splwt-lite-metabox-hide').addClass('splwt-lite-metabox-show');

      });

    }
  };

  //
  // Metabox Post Formats Listener
  //
  $.fn.splwt_post_formats = function () {
    if (this.length) {

      $(document).on('change', '.editor-post-format select, #formatdiv input[name="post_format"]', function () {

        var maybe_value = $(this).val() || 'default';

        // Fallback for classic editor version
        maybe_value = (maybe_value === '0') ? 'default' : maybe_value;

        $('.splwt-lite-post-formats').removeClass('splwt-lite-metabox-show').addClass('splwt-lite-metabox-hide');
        $('.splwt-lite-post-format-' + maybe_value).removeClass('splwt-lite-metabox-hide').addClass('splwt-lite-metabox-show');

      });

    }
  };

  //
  // Search
  //
  $.fn.splwt_search = function () {
    return this.each(function () {

      var $this = $(this),
        $input = $this.find('input');

      $input.on('change keyup', function () {

        var value = $(this).val(),
          $wrapper = $('.splwt-lite-wrapper'),
          $section = $wrapper.find('.splwt-lite-section'),
          $fields = $section.find('> .splwt-lite-field:not(.splwt-lite-depend-on)'),
          $titles = $fields.find('> .splwt-lite-title, .splwt-lite-search-tags');

        if (value.length > 3) {

          $fields.addClass('splwt-lite-metabox-hide');
          $wrapper.addClass('splwt-lite-search-all');

          $titles.each(function () {

            var $title = $(this);

            if ($title.text().match(new RegExp('.*?' + value + '.*?', 'i'))) {

              var $field = $title.closest('.splwt-lite-field');

              $field.removeClass('splwt-lite-metabox-hide');
              $field.parent().splwt_reload_script();

            }

          });

        } else {

          $fields.removeClass('splwt-lite-metabox-hide');
          $wrapper.removeClass('splwt-lite-search-all');

        }

      });

    });
  };

  //
  // Sticky Header
  //
  $.fn.splwt_sticky = function () {
    return this.each(function () {

      var $this = $(this),
        $window = $(window),
        $inner = $this.find('.splwt-lite-header-inner'),
        padding = parseInt($inner.css('padding-left')) + parseInt($inner.css('padding-right')),
        offset = 32,
        scrollTop = 0,
        lastTop = 0,
        ticking = false,
        stickyUpdate = function () {

          var offsetTop = $this.offset().top,
            stickyTop = Math.max(offset, offsetTop - scrollTop),
            winWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

          if (stickyTop <= offset && winWidth > 782) {
            $inner.css({ width: $this.outerWidth() - padding });
            $this.css({ height: $this.outerHeight() }).addClass('splwt-lite-sticky');
          } else {
            $inner.removeAttr('style');
            $this.removeAttr('style').removeClass('splwt-lite-sticky');
          }

        },
        requestTick = function () {

          if (!ticking) {
            requestAnimationFrame(function () {
              stickyUpdate();
              ticking = false;
            });
          }

          ticking = true;

        },
        onSticky = function () {

          scrollTop = $window.scrollTop();
          requestTick();

        };

      $window.on('scroll resize', onSticky);

      onSticky();

    });
  };

  //
  // Dependency System
  //
  $.fn.splwt_dependency = function () {
    return this.each(function () {

      var $this = $(this),
        $fields = $this.children('[data-controller]');

      if ($fields.length) {

        var normal_ruleset = $.splwt_deps.createRuleset(),
          global_ruleset = $.splwt_deps.createRuleset(),
          normal_depends = [],
          global_depends = [];

        $fields.each(function () {

          var $field = $(this),
            controllers = $field.data('controller').split('|'),
            conditions = $field.data('condition').split('|'),
            values = $field.data('value').toString().split('|'),
            is_global = $field.data('depend-global') ? true : false,
            ruleset = (is_global) ? global_ruleset : normal_ruleset;

          $.each(controllers, function (index, depend_id) {

            var value = values[index] || '',
              condition = conditions[index] || conditions[0];

            ruleset = ruleset.createRule('[data-depend-id="' + depend_id + '"]', condition, value);

            ruleset.include($field);

            if (is_global) {
              global_depends.push(depend_id);
            } else {
              normal_depends.push(depend_id);
            }

          });

        });

        if (normal_depends.length) {
          $.splwt_deps.enable($this, normal_ruleset, normal_depends);
        }

        if (global_depends.length) {
          $.splwt_deps.enable(SPLW.vars.$body, global_ruleset, global_depends);
        }

      }

    });
  };

  //
  // Field: code_editor
  //
  $.fn.splwt_field_code_editor = function () {
    return this.each(function () {

      if (typeof CodeMirror !== 'function') { return; }

      var $this = $(this),
        $textarea = $this.find('textarea'),
        $inited = $this.find('.CodeMirror'),
        data_editor = $textarea.data('editor');

      if ($inited.length) {
        $inited.remove();
      }

      var interval = setInterval(function () {
        if ($this.is(':visible')) {

          var code_editor = CodeMirror.fromTextArea($textarea[0], data_editor);

          // load code-mirror theme css.
          if (data_editor.theme !== 'default' && SPLW.vars.code_themes.indexOf(data_editor.theme) === -1) {

            var $cssLink = $('<link>');

            $('#splwt-lite-codemirror-css').after($cssLink);

            $cssLink.attr({
              rel: 'stylesheet',
              id: 'splwt-lite-codemirror-' + data_editor.theme + '-css',
              href: data_editor.cdnURL + '/theme/' + data_editor.theme + '.min.css',
              type: 'text/css',
              media: 'all'
            });

            SPLW.vars.code_themes.push(data_editor.theme);

          }

          CodeMirror.modeURL = data_editor.cdnURL + '/mode/%N/%N.min.js';
          CodeMirror.autoLoadMode(code_editor, data_editor.mode);

          code_editor.on('change', function (editor, event) {
            $textarea.val(code_editor.getValue()).trigger('change');
          });

          clearInterval(interval);

        }
      });

    });
  };


  //
  // Field: spinner
  //
  $.fn.splwt_field_spinner = function () {
    return this.each(function () {

      var $this = $(this),
        $input = $this.find('input'),
        $inited = $this.find('.ui-spinner-button'),
        data = $input.data();

      if ($inited.length) {
        $inited.remove();
      }

      $input.spinner({
        min: data.min || 0,
        max: data.max || 100,
        step: data.step || 1,
        create: function (event, ui) {
          if (data.unit) {
            $this.find('.ui-spinner-up').after('<span class="ui-button-text-only splwt-lite--unit">' + data.unit + '</span>');
          }
        },
        spin: function (event, ui) {
          $input.val(ui.value).trigger('change');
        }
      });

    });
  };

  //
  // Field: switcher
  //
  $.fn.splwt_field_switcher = function () {
    return this.each(function () {

      var $switcher = $(this).find('.splwt-lite--switcher');

      $switcher.on('click', function () {

        var value = 0;
        var $input = $switcher.find('input');

        if ($switcher.hasClass('splwt-lite--active')) {
          $switcher.removeClass('splwt-lite--active');
        } else {
          value = 1;
          $switcher.addClass('splwt-lite--active');
        }

        $input.val(value).trigger('change');

      });

    });
  };

  //
  // Confirm
  //
  $.fn.splwt_confirm = function () {
    return this.each(function () {
      $(this).on('click', function (e) {

        var confirm_text = $(this).data('confirm') || window.splwt_vars.i18n.confirm;
        var confirm_answer = confirm(confirm_text);

        if (confirm_answer) {
          SPLW.vars.is_confirm = true;
          SPLW.vars.form_modified = false;
        } else {
          e.preventDefault();
          return false;
        }

      });
    });
  };

  $.fn.serializeObject = function () {

    var obj = {};

    $.each(this.serializeArray(), function (i, o) {
      var n = o.name,
        v = o.value;

      obj[n] = obj[n] === undefined ? v
        : $.isArray(obj[n]) ? obj[n].concat(v)
          : [obj[n], v];
    });

    return obj;

  };

  //
  // Options Save
  //
  $.fn.splwt_save = function () {
    return this.each(function () {

      var $this = $(this),
        $buttons = $('.splwt-lite-save'),
        $panel = $('.splwt-lite-options'),
        flooding = false,
        timeout;

      $this.on('click', function (e) {

        if (!flooding) {

          var $text = $this.data('save'),
            $value = $this.val();

          $buttons.attr('value', $text);

          if ($this.hasClass('splwt-lite-save-ajax')) {

            e.preventDefault();

            $panel.addClass('splwt-lite-saving');
            $buttons.prop('disabled', true);

            window.wp.ajax.post('splwt_' + $panel.data('unique') + '_ajax_save', {
              data: $('#splwt-lite-form').serializeJSONSPLWT()
            })
              .done(function (response) {

                // clear errors
                $('.splwt-lite-error').remove();

                if (Object.keys(response.errors).length) {

                  var error_icon = '<i class="splwt-lite-label-error splwt-lite-error">!</i>';

                  $.each(response.errors, function (key, error_message) {

                    var $field = $('[data-depend-id="' + key + '"]'),
                      $link = $('#splwt-lite-tab-link-' + ($field.closest('.splwt-lite-section').index() + 1)),
                      $tab = $link.closest('.splwt-lite-tab-depth-0');

                    $field.closest('.splwt-lite-fieldset').append('<p class="splwt-lite-error splwt-lite-error-text">' + error_message + '</p>');

                    if (!$link.find('.splwt-lite-error').length) {
                      $link.append(error_icon);
                    }

                    if (!$tab.find('.splwt-lite-arrow .splwt-lite-error').length) {
                      $tab.find('.splwt-lite-arrow').append(error_icon);
                    }

                  });

                }

                $panel.removeClass('splwt-lite-saving');
                $buttons.prop('disabled', false).attr('value', $value);
                flooding = false;

                SPLW.vars.form_modified = false;
                SPLW.vars.$form_warning.hide();

                clearTimeout(timeout);

                var $result_success = $('.splwt-lite-form-success');
                $result_success.empty().append(response.notice).fadeIn('fast', function () {
                  timeout = setTimeout(function () {
                    $result_success.fadeOut('fast');
                  }, 1000);
                });

              })
              .fail(function (response) {
                alert(response.error);
              });

          } else {

            SPLW.vars.form_modified = false;

          }

        }

        flooding = true;

      });

    });
  };

  //
  // Option Framework
  //
  $.fn.splwt_options = function () {
    return this.each(function () {

      var $this = $(this),
        $content = $this.find('.splwt-lite-content'),
        $form_success = $this.find('.splwt-lite-form-success'),
        $form_warning = $this.find('.splwt-lite-form-warning'),
        $save_button = $this.find('.splwt-lite-header .splwt-lite-save');

      SPLW.vars.$form_warning = $form_warning;

      // Shows a message white leaving theme options without saving
      if ($form_warning.length) {

        window.onbeforeunload = function () {
          return (SPLW.vars.form_modified) ? true : undefined;
        };

        $content.on('change keypress', ':input', function () {
          if (!SPLW.vars.form_modified) {
            $form_success.hide();
            $form_warning.fadeIn('fast');
            SPLW.vars.form_modified = true;
          }
        });

      }

      if ($form_success.hasClass('splwt-lite-form-show')) {
        setTimeout(function () {
          $form_success.fadeOut('fast');
        }, 1000);
      }

      $(document).on('keydown', function (event) {
        if ((event.ctrlKey || event.metaKey) && event.which === 83) {
          $save_button.trigger('click');
          event.preventDefault();
          return false;
        }
      });

    });
  };

  //
  // Taxonomy Framework
  //
  $.fn.splwt_taxonomy = function () {
    return this.each(function () {

      var $this = $(this),
        $form = $this.parents('form');

      if ($form.attr('id') === 'addtag') {

        var $submit = $form.find('#submit'),
          $cloned = $this.find('.splwt-lite-field').splwt_clone();

        $submit.on('click', function () {

          if (!$form.find('.form-required').hasClass('form-invalid')) {

            $this.data('inited', false);

            $this.empty();

            $this.html($cloned);

            $cloned = $cloned.splwt_clone();

            $this.splwt_reload_script();

          }

        });

      }

    });
  };

  //
  // Shortcode Framework
  //
  $.fn.splwt_shortcode = function () {

    var base = this;

    base.shortcode_parse = function (serialize, key) {

      var shortcode = '';

      $.each(serialize, function (shortcode_key, shortcode_values) {

        key = (key) ? key : shortcode_key;

        shortcode += '[' + key;

        $.each(shortcode_values, function (shortcode_tag, shortcode_value) {

          if (shortcode_tag === 'content') {

            shortcode += ']';
            shortcode += shortcode_value;
            shortcode += '[/' + key + '';

          } else {

            shortcode += base.shortcode_tags(shortcode_tag, shortcode_value);

          }

        });

        shortcode += ']';

      });

      return shortcode;

    };

    base.shortcode_tags = function (shortcode_tag, shortcode_value) {

      var shortcode = '';

      if (shortcode_value !== '') {

        if (typeof shortcode_value === 'object' && !$.isArray(shortcode_value)) {

          $.each(shortcode_value, function (sub_shortcode_tag, sub_shortcode_value) {

            // sanitize spesific key/value
            switch (sub_shortcode_tag) {

              case 'background-image':
                sub_shortcode_value = (sub_shortcode_value.url) ? sub_shortcode_value.url : '';
                break;

            }

            if (sub_shortcode_value !== '') {
              shortcode += ' ' + sub_shortcode_tag.replace('-', '_') + '="' + sub_shortcode_value.toString() + '"';
            }

          });

        } else {

          shortcode += ' ' + shortcode_tag.replace('-', '_') + '="' + shortcode_value.toString() + '"';

        }

      }

      return shortcode;

    };

    base.insertAtChars = function (_this, currentValue) {

      var obj = (typeof _this[0].name !== 'undefined') ? _this[0] : _this;

      if (obj.value.length && typeof obj.selectionStart !== 'undefined') {
        obj.focus();
        return obj.value.substring(0, obj.selectionStart) + currentValue + obj.value.substring(obj.selectionEnd, obj.value.length);
      } else {
        obj.focus();
        return currentValue;
      }

    };

    base.send_to_editor = function (html, editor_id) {

      var tinymce_editor;

      if (typeof tinymce !== 'undefined') {
        tinymce_editor = tinymce.get(editor_id);
      }

      if (tinymce_editor && !tinymce_editor.isHidden()) {
        tinymce_editor.execCommand('mceInsertContent', false, html);
      } else {
        var $editor = $('#' + editor_id);
        $editor.val(base.insertAtChars($editor, html)).trigger('change');
      }

    };

    return this.each(function () {

      var $modal = $(this),
        $load = $modal.find('.splwt-lite-modal-load'),
        $content = $modal.find('.splwt-lite-modal-content'),
        $insert = $modal.find('.splwt-lite-modal-insert'),
        $loading = $modal.find('.splwt-lite-modal-loading'),
        $select = $modal.find('select'),
        modal_id = $modal.data('modal-id'),
        nonce = $modal.data('nonce'),
        editor_id,
        target_id,
        sc_key,
        sc_name,
        sc_view,
        sc_group,
        $cloned,
        $button;

      $(document).on('click', '.splwt-lite-shortcode-button[data-modal-id="' + modal_id + '"]', function (e) {

        e.preventDefault();

        $button = $(this);
        editor_id = $button.data('editor-id') || false;
        target_id = $button.data('target-id') || false;

        $modal.removeClass('hidden');

        // single usage trigger first shortcode
        if ($modal.hasClass('splwt-lite-shortcode-single') && sc_name === undefined) {
          $select.trigger('change');
        }

      });

      $select.on('change', function () {

        var $option = $(this);
        var $selected = $option.find(':selected');

        sc_key = $option.val();
        sc_name = $selected.data('shortcode');
        sc_view = $selected.data('view') || 'normal';
        sc_group = $selected.data('group') || sc_name;

        $load.empty();

        if (sc_key) {

          $loading.show();

          window.wp.ajax.post('splwt-lite-get-shortcode-' + modal_id, {
            shortcode_key: sc_key,
            nonce: nonce
          })
            .done(function (response) {

              $loading.hide();

              var $appended = $(response.content).appendTo($load);

              $insert.parent().removeClass('hidden');

              $cloned = $appended.find('.splwt-lite--repeat-shortcode').splwt_clone();

              $appended.splwt_reload_script();
              $appended.find('.splwt-lite-fields').splwt_reload_script();

            });

        } else {

          $insert.parent().addClass('hidden');

        }

      });

      $insert.on('click', function (e) {

        e.preventDefault();

        if ($insert.prop('disabled') || $insert.attr('disabled')) { return; }

        var shortcode = '';
        var serialize = $modal.find('.splwt-lite-field:not(.splwt-lite-depend-on)').find(':input:not(.ignore)').serializeObjectSPLWT();

        switch (sc_view) {

          case 'contents':
            var contentsObj = (sc_name) ? serialize[sc_name] : serialize;
            $.each(contentsObj, function (sc_key, sc_value) {
              var sc_tag = (sc_name) ? sc_name : sc_key;
              shortcode += '[' + sc_tag + ']' + sc_value + '[/' + sc_tag + ']';
            });
            break;

          case 'group':

            shortcode += '[' + sc_name;
            $.each(serialize[sc_name], function (sc_key, sc_value) {
              shortcode += base.shortcode_tags(sc_key, sc_value);
            });
            shortcode += ']';
            shortcode += base.shortcode_parse(serialize[sc_group], sc_group);
            shortcode += '[/' + sc_name + ']';

            break;

          case 'repeater':
            shortcode += base.shortcode_parse(serialize[sc_group], sc_group);
            break;

          default:
            shortcode += base.shortcode_parse(serialize);
            break;

        }

        shortcode = (shortcode === '') ? '[' + sc_name + ']' : shortcode;

        if (editor_id) {

          base.send_to_editor(shortcode, editor_id);

        } else {

          var $textarea = (target_id) ? $(target_id) : $button.parent().find('textarea');
          $textarea.val(base.insertAtChars($textarea, shortcode)).trigger('change');

        }

        $modal.addClass('hidden');

      });

      $modal.on('click', '.splwt-lite--repeat-button', function (e) {

        e.preventDefault();

        var $repeatable = $modal.find('.splwt-lite--repeatable');
        var $new_clone = $cloned.splwt_clone();
        var $remove_btn = $new_clone.find('.splwt-lite-repeat-remove');

        var $appended = $new_clone.appendTo($repeatable);

        $new_clone.find('.splwt-lite-fields').splwt_reload_script();

        SPLW.helper.name_nested_replace($modal.find('.splwt-lite--repeat-shortcode'), sc_group);

        $remove_btn.on('click', function () {

          $new_clone.remove();

          SPLW.helper.name_nested_replace($modal.find('.splwt-lite--repeat-shortcode'), sc_group);

        });

      });

      $modal.on('click', '.splwt-lite-modal-close, .splwt-lite-modal-overlay', function () {
        $modal.addClass('hidden');
      });

    });
  };

  //
  // WP Color Picker
  //
  if (typeof Color === 'function') {

    Color.prototype.toString = function () {

      if (this._alpha < 1) {
        return this.toCSS('rgba', this._alpha).replace(/\s+/g, '');
      }

      var hex = parseInt(this._color, 10).toString(16);

      if (this.error) { return ''; }

      if (hex.length < 6) {
        for (var i = 6 - hex.length - 1; i >= 0; i--) {
          hex = '0' + hex;
        }
      }

      return '#' + hex;

    };

  }

  SPLW.funcs.parse_color = function (color) {

    var value = color.replace(/\s+/g, ''),
      trans = (value.indexOf('rgba') !== -1) ? parseFloat(value.replace(/^.*,(.+)\)/, '$1') * 100) : 100,
      rgba = (trans < 100) ? true : false;

    return { value: value, transparent: trans, rgba: rgba };

  };

  $.fn.splwt_color = function () {
    return this.each(function () {

      var $input = $(this),
        picker_color = SPLW.funcs.parse_color($input.val()),
        palette_color = window.splwt_vars.color_palette.length ? window.splwt_vars.color_palette : true,
        $container;

      // Destroy and Reinit
      if ($input.hasClass('wp-color-picker')) {
        $input.closest('.wp-picker-container').after($input).remove();
      }

      $input.wpColorPicker({
        palettes: palette_color,
        change: function (event, ui) {

          var ui_color_value = ui.color.toString();

          $container.removeClass('splwt-lite--transparent-active');
          $container.find('.splwt-lite--transparent-offset').css('background-color', ui_color_value);
          $input.val(ui_color_value).trigger('change');

        },
        create: function () {

          $container = $input.closest('.wp-picker-container');

          var a8cIris = $input.data('a8cIris'),
            $transparent_wrap = $('<div class="splwt-lite--transparent-wrap">' +
              '<div class="splwt-lite--transparent-slider"></div>' +
              '<div class="splwt-lite--transparent-offset"></div>' +
              '<div class="splwt-lite--transparent-text"></div>' +
              '<div class="splwt-lite--transparent-button">transparent <i class="fas fa-toggle-off"></i></div>' +
              '</div>').appendTo($container.find('.wp-picker-holder')),
            $transparent_slider = $transparent_wrap.find('.splwt-lite--transparent-slider'),
            $transparent_text = $transparent_wrap.find('.splwt-lite--transparent-text'),
            $transparent_offset = $transparent_wrap.find('.splwt-lite--transparent-offset'),
            $transparent_button = $transparent_wrap.find('.splwt-lite--transparent-button');

          if ($input.val() === 'transparent') {
            $container.addClass('splwt-lite--transparent-active');
          }

          $transparent_button.on('click', function () {
            if ($input.val() !== 'transparent') {
              $input.val('transparent').trigger('change').removeClass('iris-error');
              $container.addClass('splwt-lite--transparent-active');
            } else {
              $input.val(a8cIris._color.toString()).trigger('change');
              $container.removeClass('splwt-lite--transparent-active');
            }
          });

          $transparent_slider.slider({
            value: picker_color.transparent,
            step: 1,
            min: 0,
            max: 100,
            slide: function (event, ui) {

              var slide_value = parseFloat(ui.value / 100);
              a8cIris._color._alpha = slide_value;
              $input.wpColorPicker('color', a8cIris._color.toString());
              $transparent_text.text((slide_value === 1 || slide_value === 0 ? '' : slide_value));

            },
            create: function () {

              var slide_value = parseFloat(picker_color.transparent / 100),
                text_value = slide_value < 1 ? slide_value : '';

              $transparent_text.text(text_value);
              $transparent_offset.css('background-color', picker_color.value);

              $container.on('click', '.wp-picker-clear', function () {

                a8cIris._color._alpha = 1;
                $transparent_text.text('');
                $transparent_slider.slider('option', 'value', 100);
                $container.removeClass('splwt-lite--transparent-active');
                $input.trigger('change');

              });

              $container.on('click', '.wp-picker-default', function () {

                var default_color = SPLW.funcs.parse_color($input.data('default-color')),
                  default_value = parseFloat(default_color.transparent / 100),
                  default_text = default_value < 1 ? default_value : '';

                a8cIris._color._alpha = default_value;
                $transparent_text.text(default_text);
                $transparent_slider.slider('option', 'value', default_color.transparent);

              });

            }
          });
        }
      });

    });
  };

  //
  // ChosenJS
  //
  $.fn.splwt_chosen = function () {
    return this.each(function () {

      var $this = $(this),
        $inited = $this.parent().find('.chosen-container'),
        is_sortable = $this.hasClass('splwt-lite-chosen-sortable') || false,
        is_ajax = $this.hasClass('splwt-lite-chosen-ajax') || false,
        is_multiple = $this.attr('multiple') || false,
        set_width = is_multiple ? '100%' : 'auto',
        set_options = $.extend({
          allow_single_deselect: true,
          disable_search_threshold: 10,
          width: set_width,
          no_results_text: window.splwt_vars.i18n.no_results_text,
        }, $this.data('chosen-settings'));

      if ($inited.length) {
        $inited.remove();
      }

      // Chosen ajax
      if (is_ajax) {

        var set_ajax_options = $.extend({
          data: {
            type: 'post',
            nonce: '',
          },
          allow_single_deselect: true,
          disable_search_threshold: -1,
          width: '100%',
          min_length: 3,
          type_delay: 500,
          typing_text: window.splwt_vars.i18n.typing_text,
          searching_text: window.splwt_vars.i18n.searching_text,
          no_results_text: window.splwt_vars.i18n.no_results_text,
        }, $this.data('chosen-settings'));

        $this.SPLWTAjaxChosen(set_ajax_options);

      } else {

        $this.chosen(set_options);

      }

      // Chosen keep options order
      if (is_multiple) {

        var $hidden_select = $this.parent().find('.splwt-lite-hide-select');
        var $hidden_value = $hidden_select.val() || [];

        $this.on('change', function (obj, result) {

          if (result && result.selected) {
            $hidden_select.append('<option value="' + result.selected + '" selected="selected">' + result.selected + '</option>');
          } else if (result && result.deselected) {
            $hidden_select.find('option[value="' + result.deselected + '"]').remove();
          }

          // Force customize refresh
          if (window.wp.customize !== undefined && $hidden_select.children().length === 0 && $hidden_select.data('customize-setting-link')) {
            window.wp.customize.control($hidden_select.data('customize-setting-link')).setting.set('');
          }

          $hidden_select.trigger('change');

        });

        // Chosen order abstract
        $this.SPLWTChosenOrder($hidden_value, true);

      }

      // Chosen sortable
      if (is_sortable) {

        var $chosen_container = $this.parent().find('.chosen-container');
        var $chosen_choices = $chosen_container.find('.chosen-choices');

        $chosen_choices.bind('mousedown', function (event) {
          if ($(event.target).is('span')) {
            event.stopPropagation();
          }
        });

        $chosen_choices.sortable({
          items: 'li:not(.search-field)',
          helper: 'orginal',
          cursor: 'move',
          placeholder: 'search-choice-placeholder',
          start: function (e, ui) {
            ui.placeholder.width(ui.item.innerWidth());
            ui.placeholder.height(ui.item.innerHeight());
          },
          update: function (e, ui) {

            var select_options = '';
            var chosen_object = $this.data('chosen');
            var $prev_select = $this.parent().find('.splwt-lite-hide-select');

            $chosen_choices.find('.search-choice-close').each(function () {
              var option_array_index = $(this).data('option-array-index');
              $.each(chosen_object.results_data, function (index, data) {
                if (data.array_index === option_array_index) {
                  select_options += '<option value="' + data.value + '" selected>' + data.value + '</option>';
                }
              });
            });

            $prev_select.children().remove();
            $prev_select.append(select_options);
            $prev_select.trigger('change');

          }
        });

      }

    });
  };

  //
  // Helper Checkbox Checker
  //
  $.fn.splwt_checkbox = function () {
    return this.each(function () {

      var $this = $(this),
        $input = $this.find('.splwt-lite--input'),
        $checkbox = $this.find('.splwt-lite--checkbox');

      $checkbox.on('click', function () {
        $input.val(Number($checkbox.prop('checked'))).trigger('change');
      });

    });
  };

  //
  // Siblings
  //
  $.fn.splwt_siblings = function () {
    return this.each(function () {

      var $this = $(this),
        $siblings = $this.find('.splwt-lite--sibling'),
        multiple = $this.data('multiple') || false;

      $siblings.on('click', function () {

        var $sibling = $(this);

        if (multiple) {

          if ($sibling.hasClass('splwt-lite--active')) {
            $sibling.removeClass('splwt-lite--active');
            $sibling.find('input').prop('checked', false).trigger('change');
          } else {
            $sibling.addClass('splwt-lite--active');
            $sibling.find('input').prop('checked', true).trigger('change');
          }

        } else {

          $this.find('input').prop('checked', false);
          $sibling.find('input').prop('checked', true).trigger('change');
          $sibling.addClass('splwt-lite--active').siblings().removeClass('splwt-lite--active');

        }

      });

    });
  };

  //
  // Help Tooltip
  //
  $.fn.splwt_help = function () {
    return this.each(function () {

      var $this = $(this),
        $tooltip,
        offset_left;

      $this.on({
        mouseenter: function () {

          $tooltip = $('<div class="splwt-lite-tooltip"></div>').html($this.find('.splwt-lite-help-text').html()).appendTo('body');
          offset_left = (SPLW.vars.is_rtl) ? ($this.offset().left - $tooltip.outerWidth()) : ($this.offset().left + 24);

          $tooltip.css({
            top: $this.offset().top - (($tooltip.outerHeight() / 2) - 14),
            left: offset_left,
          });

        },
        mouseleave: function () {

          if ($tooltip !== undefined) {
            $tooltip.remove();
          }

        }

      });

    });
  };

  //
  // Customize Refresh
  //
  $.fn.splwt_customizer_refresh = function () {
    return this.each(function () {

      var $this = $(this),
        $complex = $this.closest('.splwt-lite-customize-complex');

      if ($complex.length) {

        var $input = $complex.find(':input'),
          $unique = $complex.data('unique-id'),
          $option = $complex.data('option-id'),
          obj = $input.serializeObjectSPLWT(),
          data = (!$.isEmptyObject(obj)) ? obj[$unique][$option] : '',
          control = window.wp.customize.control($unique + '[' + $option + ']');

        // clear the value to force refresh.
        control.setting._value = null;

        control.setting.set(data);

      } else {

        $this.find(':input').first().trigger('change');

      }

      $(document).trigger('splwt-lite-customizer-refresh', $this);

    });
  };

  //
  // Customize Listen Form Elements
  //
  $.fn.splwt_customizer_listen = function (options) {

    var settings = $.extend({
      closest: false,
    }, options);

    return this.each(function () {

      if (window.wp.customize === undefined) { return; }

      var $this = (settings.closest) ? $(this).closest('.splwt-lite-customize-complex') : $(this),
        $input = $this.find(':input'),
        unique_id = $this.data('unique-id'),
        option_id = $this.data('option-id');

      if (unique_id === undefined) { return; }

      $input.on('change keyup', SPLW.helper.debounce(function () {

        var obj = $this.find(':input').serializeObjectSPLWT();
        var val = (!$.isEmptyObject(obj) && obj[unique_id] && obj[unique_id][option_id]) ? obj[unique_id][option_id] : '';

        window.wp.customize.control(unique_id + '[' + option_id + ']').setting.set(val);

      }, 250));

    });
  };

  //
  // Customizer Listener for Reload JS
  //
  $(document).on('expanded', '.control-section', function () {

    var $this = $(this);

    if ($this.hasClass('open') && !$this.data('inited')) {

      var $fields = $this.find('.splwt-lite-customize-field');
      var $complex = $this.find('.splwt-lite-customize-complex');

      if ($fields.length) {
        $this.splwt_dependency();
        $fields.splwt_reload_script({ dependency: false });
        $complex.splwt_customizer_listen();
      }

      $this.data('inited', true);

    }

  });

  //
  // Window on resize
  //
  SPLW.vars.$window.on('resize splwt-lite.resize', SPLW.helper.debounce(function (event) {

    var window_width = navigator.userAgent.indexOf('AppleWebKit/') > -1 ? SPLW.vars.$window.width() : window.innerWidth;

    if (window_width <= 782 && !SPLW.vars.onloaded) {
      $('.splwt-lite-section').splwt_reload_script();
      SPLW.vars.onloaded = true;
    }

  }, 200)).trigger('splwt-lite.resize');

  //
  // Widgets Framework
  //
  $.fn.splwt_widgets = function () {
    if (this.length) {

      $(document).on('widget-added widget-updated', function (event, $widget) {
        $widget.find('.splwt-lite-fields').splwt_reload_script();
      });

      $('.widgets-sortables, .control-section-sidebar').on('sortstop', function (event, ui) {
        ui.item.find('.splwt-lite-fields').splwt_reload_script_retry();
      });

      $(document).on('click', '.widget-top', function (event) {
        $(this).parent().find('.splwt-lite-fields').splwt_reload_script();
      });

    }
  };

  //
  // Nav Menu Options Framework
  //
  $.fn.splwt_nav_menu = function () {
    return this.each(function () {

      var $navmenu = $(this);

      $navmenu.on('click', 'a.item-edit', function () {
        $(this).closest('li.menu-item').find('.splwt-lite-fields').splwt_reload_script();
      });

      $navmenu.on('sortstop', function (event, ui) {
        ui.item.find('.splwt-lite-fields').splwt_reload_script_retry();
      });

    });
  };

  //
  // Retry Plugins
  //
  $.fn.splwt_reload_script_retry = function () {
    return this.each(function () {

      var $this = $(this);

    });
  };

  //
  // Reload Plugins
  //
  $.fn.splwt_reload_script = function (options) {

    var settings = $.extend({
      dependency: true,
    }, options);

    return this.each(function () {

      var $this = $(this);

      // Avoid for conflicts
      if (!$this.data('inited')) {

        // Field plugins
        $this.children('.splwt-lite-field-code_editor').splwt_field_code_editor();
        $this.children('.splwt-lite-field-spinner').splwt_field_spinner();
        $this.children('.splwt-lite-field-switcher').splwt_field_switcher();

        // Field colors
        $this.children('.splwt-lite-field-border').find('.splwt-lite-color').splwt_color();
        $this.children('.splwt-lite-field-color').find('.splwt-lite-color').splwt_color();
        $this.children('.splwt-lite-field-color_group').find('.splwt-lite-color').splwt_color();
        $this.children('.splwt-lite-field-typography').find('.splwt-lite-color').splwt_color();

        // Field chosenjs
        $this.children('.splwt-lite-field-select').find('.splwt-lite-chosen').splwt_chosen();

        // Field Checkbox
        $this.children('.splwt-lite-field-checkbox').find('.splwt-lite-checkbox').splwt_checkbox();

        // Field Siblings
        $this.children('.splwt-lite-field-button_set').find('.splwt-lite-siblings').splwt_siblings();

        // Help Tooptip
        $this.children('.splwt-lite-field').find('.splwt-lite-help').splwt_help();

        if (settings.dependency) {
          $this.splwt_dependency();
        }

        $this.data('inited', true);

        $(document).trigger('splwt-lite-reload-script', $this);

      }

    });
  };

  //
  // Document ready and run scripts
  //
  $(document).ready(function () {

    $('.splwt-lite-save').splwt_save();
    $('.splwt-lite-options').splwt_options();
    $('.splwt-lite-sticky-header').splwt_sticky();
    $('.splwt-lite-nav-options').splwt_nav_options();
    $('.splwt-lite-nav-metabox').splwt_nav_metabox();
    $('.splwt-lite-taxonomy').splwt_taxonomy();
    $('.splwt-lite-page-templates').splwt_page_templates();
    $('.splwt-lite-post-formats').splwt_post_formats();
    $('.splwt-lite-shortcode').splwt_shortcode();
    $('.splwt-lite-search').splwt_search();
    $('.splwt-lite-confirm').splwt_confirm();
    $('.splwt-lite-expand-all').splwt_expand_all();
    $('.splwt-lite-onload').splwt_reload_script();
    $('.widget').splwt_widgets();
    $('#menu-to-edit').splwt_nav_menu();


    // weather location lite to pro option disabled.
    $('.splw_display_weather').find(".splwt-lite--sibling.splwt-lite--button:nth-of-type(2)").attr('disabled', 'disabled').addClass('splw_pro_only');

    $('.splw_custom_button_fields').find(".splwt-lite--sibling.splwt-lite--button:nth-of-type(3)").attr('disabled', 'disabled').addClass('splw_pro_only');

    $('.splw_get_weather_by').find(".splwt-lite--sibling.splwt-lite--button:nth-of-type(2),.splwt-lite--sibling.splwt-lite--button:nth-of-type(3),.splwt-lite--sibling.splwt-lite--button:nth-of-type(4)").attr('disabled', 'disabled').addClass('splw_pro_only');

    $('.splw_pressure_unit').find("select option[value='1'],option[value='2'],option[value='3'],option[value='4'],option[value='5']").attr('disabled', 'disabled').addClass('splw_pro_only');

    $('.splw_wind_speed_unit').find("select option[value='3'],option[value='4'],option[value='5']").attr('disabled', 'disabled').addClass('splw_pro_only');
    $('.splw_background_type').find("select option[value='1'],option[value='2'],option[value='3']").attr('disabled', 'disabled').addClass('splw_pro_only');
    $('.splw_forecast_days').find("select").attr('disabled', 'disabled').addClass('splw_pro_only');
    $('.splw_show_hide').find(".splwt-lite--switcher").attr('disabled', 'disabled').addClass('splw_pro_only').css({ "background": "#CBCFD0" });

    $('.splw_pro_only').css({ 'pointer-events': 'none', 'color': '#9a9a9a', "position": "relative" });

    $(window).off('beforeunload');

    $(".splw-publish-button").on('click',function () {
      $(".spinner").css({ "display": "inline-block", "visibility": "visible" });

    })

    /* Copy to clipboard */
    $('.splw__shortcode').on('click',function (e) {
      e.preventDefault();
      /* Get the text field */
      var copyText = $(this);
      /* Select the text field */
      copyText.trigger('select');
      document.execCommand("copy");
      jQuery(".splw-after-copy-text").animate({
        opacity: 1,
        bottom: 25
      }, 300);
      setTimeout(function () {
        jQuery(".splw-after-copy-text").animate({
          opacity: 0,
        }, 200);
        jQuery(".splw-after-copy-text").animate({
          bottom: 0
        }, 0);
      }, 2000);
    });
    $('.splw-copy').on('click',function (e) {
      e.preventDefault();
      splw_copyToClipboard($(this));
      splw_SelectText($(this));
      $(this).trigger('focus select');
      jQuery(".splw-after-copy-text").animate({
        opacity: 1,
        bottom: 25
      }, 300);
      setTimeout(function () {
        jQuery(".splw-after-copy-text").animate({
          opacity: 0,
        }, 200);
        jQuery(".splw-after-copy-text").animate({
          bottom: 0
        }, 0);
      }, 2000);
    });

    function splw_copyToClipboard(element) {
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val($(element).text()).trigger('select');
      document.execCommand("copy");
      $temp.remove();
    }

    function splw_SelectText(element) {
      var r = document.createRange();
      var w = element.get(0);
      r.selectNodeContents(w);
      var sel = window.getSelection();
      sel.removeAllRanges();
      sel.addRange(r);
    }

	  function isValidJSONString(str) {
		  try {
			  JSON.parse(str);
		  } catch (e) {
			  return false;
		  }
		  return true;
	  }
	  //Location Weather Export.
	  var $export_type = $('.splw_what_export').find('input:checked').val();

	  $('.splw_what_export').on('change', function () {
		  $export_type = $(this).find('input:checked').val();
	  });
	  $('.location_weather_export .splwt-lite--sibling.splwt-lite--button').on('click',function (event) {
		  event.preventDefault();
		  var $shortcode_ids = $('.splw_post_ids select').val();

		  var $ex_nonce = $('#splwt_options_noncelocation_weather_tools_options').val();
		  var selected_shortcode = $export_type === 'selected_shortcodes' ? $shortcode_ids : 'all_shortcodes';
		  if ( $export_type === 'all_shortcodes' || ( $export_type === 'selected_shortcodes' && selected_shortcode.length > 0 )  ) {
			  var data = {
				  action: 'splw_export_shortcodes',
				  splw_ids: selected_shortcode,
				  nonce: $ex_nonce,
			  }

        $.post(ajaxurl, data, function (response) {
          if (response) {
            // Convert JSON Array to string.
            if (isValidJSONString(response)) {
              var json = JSON.stringify(JSON.parse(response));
            } else {
              var json = JSON.stringify(response);
            }
            // Convert JSON string to BLOB.
            var blob = new Blob([json], { type: 'application/json' });
            var link = document.createElement('a');
            var lw_time = $.now();
            link.href = window.URL.createObjectURL(blob);
            link.download = "location-weather-export-" + lw_time + ".json";
            link.click();
            $('.splwt-lite-form-result.splwt-lite-form-success').text('Exported successfully!').show();
            setTimeout(function () {
              $('.splwt-lite-form-result.splwt-lite-form-success').hide().text('');
              $('.splw_post_ids select').val('').trigger('chosen:updated');
            }, 3000);
          }
        });
		  } else {
			  $('.splwt-lite-form-result.splwt-lite-form-success').text('No shortcode selected.').show();
			  setTimeout(function () {
				  $('.splwt-lite-form-result.splwt-lite-form-success').hide().text('');
			  }, 3000);
		  }
	  });

    // Location Weather Import.
    $('.lw_import button.import').on('click',function (event) {
      event.preventDefault();	
      var $this = $(this),
      button_label = $(this).text();
      var splw_shortcodes = $('#import').prop('files')[0];
      if ($('#import').val() != '') {
        $this.append('<span class="splwt-loading-spinner"><i class="fa fa-spinner" aria-hidden="true"></i></span>')
        .css('opacity', '0.7');
        var $im_nonce = $('#splwt_options_noncelocation_weather_tools_options').val();
        var reader = new FileReader();
        reader.readAsText(splw_shortcodes);
        reader.onload = function (event) {
          var jsonObj = JSON.stringify(event.target.result);
          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              shortcode: jsonObj,
              action: 'splw_import_shortcodes',
              nonce: $im_nonce,
            },
            success: function (response) {
              $this.html(button_label).css('opacity', '1');
              $('.splwt-lite-form-result.splwt-lite-form-success').text('Imported successfully!').show();
              setTimeout(function () {
                $('.splwt-lite-form-result.splwt-lite-form-success').hide().text('');
                $('#import').val('');
                window.location.replace($('#splw_shortcode_link_redirect').attr('href'));
              }, 2000);
            },
            error: function (error) { 
              $('#import').val('');
              $this.html(button_label).css('opacity', '1');
              $('.splwt-lite-form-result.splwt-lite-form-success').addClass('splwt-lite-form-warning')
              .text('Something went wrong, please try again!').show();
              setTimeout(function () {
                $('.splwt-lite-form-result.splwt-lite-form-success').hide().text('').removeClass('splwt-lite-form-warning');
              }, 2000);
            }
          });
        }
      } else {
        $('.splwt-lite-form-result.splwt-lite-form-success').text('No exported json file chosen.').show();
        setTimeout(function () {
          $('.splwt-lite-form-result.splwt-lite-form-success').hide().text('');
        }, 3000);
      }
    });
  });
  $(document).on('keyup change', '.splwt-lite-options #splwt-lite-form', function (e) {
    e.preventDefault();
    var $button = $(this).find('.splwt-lite-save');
    $button.css({ "background-color": "#00C263", "pointer-events": "initial" }).val('Save Settings');
  });
  $('.splwt-lite-options .splwt-lite-save').on('click',function (e) {
    e.preventDefault();
    $(this).css({ "background-color": "#C5C5C6", "pointer-events": "none" }).val('Changes Saved');
  })

  // Live Preview script.
	var preview_box = $('#sp_location_weather-preview-box');
	var preview_display = $('#sp_location_weather_live_preview').hide();
	$(document).on('click', '#sp_location_weather-show-preview:contains(Hide)', function (e) {
		e.preventDefault();
		var _this = $(this);
		_this.html('<i class="fa fa-eye" aria-hidden="true"></i> Show Preview');
		preview_box.html('');
		preview_display.hide();
	});

	$(document).on('click', '#sp_location_weather-show-preview:not(:contains(Hide))', function (e) {
		e.preventDefault();
		var previewJS = window.splwt_vars.previewJS;
		var _data = $('form#post').serialize();
		var _this = $(this);
		var data = {
			action: 'sp_location_weather_preview_meta_box',
			data: _data,
			ajax_nonce: $('#splwt_metabox_noncesp_location_weather_live_preview').val()
		};

		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			error: function (response) {
				console.log(response)
			},
			success: function (response) {
				preview_display.show();
				preview_box.html(response);
        _this.html('<i class="fa fa-eye-slash" aria-hidden="true"></i> Hide Preview');
				$.getScript(previewJS, function () {
					$(document).on('keyup change', function (e) {
						e.preventDefault();
						_this.html('<i class="fa fa-refresh" aria-hidden="true"></i> Update Preview');
					});
					$("html, body").animate({ scrollTop: preview_display.offset().top - 50 }, "slow");
				})
			}
		})
	});
})(jQuery, window, document);
