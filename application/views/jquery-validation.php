<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<script>
    const ajax_error_message_rsp = (jqXHR, exception) => {
        let _ajx_error_msg = "";
        
        if (jqXHR.status === 0) {
            _ajx_error_msg = "Not connect.\n Verify Network.";
        } else if (jqXHR.status == 404) {
            _ajx_error_msg = "Requested page not found. [404]";
        } else if (jqXHR.status == 500) {
            _ajx_error_msg = "Internal Server Error [500].";
        } else if (exception === "parsererror") {
            _ajx_error_msg = "Requested JSON parse failed.";
        } else if (exception === "timeout") {
            _ajx_error_msg = "Time out error.";
        } else if (exception === "abort") {
            _ajx_error_msg = "Ajax request aborted.";
        } else {
            try {
                const responseJson = JSON.parse(jqXHR.responseText);
                _ajx_error_msg = responseJson.message || "Unknown error occurred";
            } catch (e) {
                _ajx_error_msg = "Uncaught Error.";
            }
        }

        return _ajx_error_msg;
    }

    const addValidationMethods = () => {
        $.validator.addMethod('accept', function(value, element, param) {
            // Split the param string by comma to get the list of acceptable extensions
            let typeParam = typeof param === 'string' ? param.replace(/\s/g, '') : 'image/*',
                optionalValue = this.optional(element),
                i, file;
        
            // If the field is optional and no file is selected, validation passes
            if (optionalValue) {
                return optionalValue;
            }
        
            if ($(element).attr('type') === 'file') {
                typeParam = typeParam.split(/[,|]/);
                file = element.files;
        
                for (i = 0; i < file.length; i++) {
                    // Get the file extension
                    const extension = file[i].name.substring(file[i].name.lastIndexOf('.')).toLowerCase();

                    // Check if the file extension is in the allowed list
                    if ($.inArray(extension, typeParam) === -1) {
                        return false;
                    }
                }
            }
            return true;
        }, $.validator.format("Please select valid file."));

        $.validator.addMethod('email_id', function(email, element, param) {
            if (email === "") {
                return true;
            }
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        }, $.validator.format("Please enter a valid email address."));

        $.validator.addMethod('validate_mobile', function(mobile, element, param) {
            if (mobile === "") {
                return true;
            }
            const mobilePattern = /^\+?[1-9]\d{9,14}$/;
            return mobilePattern.test(mobile);
        }, $.validator.format("Please enter a valid mobile."));

        $.validator.addMethod('validate_password', function(password, element, param) {
            if (password === "") {
                return true;
            }
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+<>?/])[A-Za-z\d!@#$%^&*()_+<>?/]{8,}$/;
            return passwordPattern.test(password);
        }, $.validator.format("At least 8 characters, one uppercase, one lowercase, one number, and one special character."));
    }

    const notify = (message) => {
        Swal.fire({
          icon: 'info',
          title: 'info',
          text: message,
          showConfirmButton: true,
          timer: 5000,
          timerProgressBar: true
        });
    }

    const submitForm = (form, loader = false, data = null) => {
        if(event) event.preventDefault();
        const deferred = $.Deferred();
        const method = $(form).attr('method').toLowerCase();
        data = data ?? (method === 'post' ? new FormData(form) : $(form).serialize());
        const submitBtn = $(form).find('[type="submit"]').length ? $(form).find('[type="submit"]') : $(form).find('[type="button"]');
        const btnHtml = submitBtn.html();
        const loadScreen = $('#preloader');

        let allIsOk = true;

        $(form).find(".f-required").each(function () {
            const _label_txt = $(this).siblings("label").html() ?? $(this).data("label");
            const _input_type = this.type;

            if(_input_type) {
                const _val = this.value.trim();
        
                if (_val == "") {
                    allIsOk = false;
                    const errorElement = $(`<strong id="${this.id}-error" class="text-danger is-invalid">Please enter the ${_label_txt}</strong>`);
                    const id = errorElement.attr('id').replace(/\[\]/g, "_");
                    if($(`#${id}`).length) $(`#${id}`).remove();
                    errorElement.appendTo($(this).closest('.form-group'));
                }

                // Add event listener to remove error message on input change
                $(this).on('input change', function () {
                    const currentVal = $(this).val().trim();
                    if (currentVal !== "") {
                        $(`#${this.id}-error`).remove();
                    }
                });
            }
        });

        if(allIsOk) {
            $.ajax({
                url: $(form).attr('action'),
                type: method,
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(form).find('.error-message').html('');
                    if(loader) {
                        loadScreen.show();
                    } else {
                        submitBtn.prop('disabled', true).html(`<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`);
                    }
                },
                error: function(jqXHR) {
                    deferred.reject(jqXHR);
                    notify(ajax_error_message_rsp(jqXHR));

                    if($('.select-terminal').length) {
                        $('.select-terminal').removeClass('d-none');
                    }
                },
                success: function(response) {
                    deferred.resolve(response);

                    if(response.redirect) {
                        window.location.href = response.redirect;
                    }

                    if(response.message) {
                        notify(response.message);
                    }

                    if(response.reloadTable && typeof reloadTable !== 'undefined') {
                        reloadTable(response.reloadTable);
                        form.reset();
                    }

                    if(response.closeModal) {
                        $(`#${response.closeModal}`).modal('hide');
                    }

                    if(response.trigger) {
                        $(`#${response.trigger}`).trigger(response.triggerEvent);
                    }

                    if(response.receipt) {
                        openPdfWithNewTab(response.receipt);
                    }

                    if($(form).hasClass('general-modal-form')) {
                        const modal = $('#general-modal');

                        if (response.html) {
                            modal.find('.modal-body').html(response.html);

                            if(response.action) {
                                modal.find('form').attr('action', response.action);
                            }
                            if(response.method) {
                                modal.find('form').attr('method', response.method);
                            }
                            if(response.footer) {
                                modal.find('.modal-footer').html(response.footer);
                            }
                            if(response.title) {
                                modal.find('.modal-title').text(response.title);
                            }
                            validateForm(".general-modal-form");
                            modal.modal('show');
                        }
                    }

                    if(response.reset) {
                        form.reset();
                    }

                    if (response.triggerMethod && typeof window[response.triggerMethod] === "function") {
                        window[response.triggerMethod]();
                    }
                },
                complete: function() {
                    setTimeout(() => {
                        if(loader) {
                            loadScreen.hide();
                        } else {
                            submitBtn.prop('disabled', false).removeClass('_effect--ripple waves-effect waves-light').html(btnHtml);
                        }
                        deferred.always();
                    }, 100);
                }
            });
        }

        return deferred.promise();
    };

    (function($) {
        "use strict";

        if($('.ajax-form').length){
            addValidationMethods();

            let rules;

            if(typeof rule == 'undefined') {
                const isProfileOrUpdate = false;

                rules = {
                    password: {
                        required: !isProfileOrUpdate,
                        validate_password: true,
                        maxlength: 50
                    },
                    password_confirmation: {
                        required: !isProfileOrUpdate,
                        maxlength: 50,
                        equalTo: "#password"
                    },
                    phone: {
                        required: !isProfileOrUpdate,
                        validate_mobile: true,
                        digits: true
                    },
                    otp: {
                        required: true,
                        digits: true,
                        maxlength: 6,
                        minlength: 6
                    },
                    email: {
                        required: !isProfileOrUpdate,
                        maxlength: 100,
                        email: true,
                        email_id: true
                    },
                    alternative_email: {
                        maxlength: 100,
                        email: true,
                        email_id: true
                    },
                    alternative_contact_number: {
                        validate_mobile: true,
                        digits: true
                    },
                    name: {
                        required: true,
                        maxlength: 100
                    }
                };
            } else {
                rules = rule;
            }

            $(".ajax-form").each(function() {
                $(this).validate({
                    rules: rules,
                    errorElement: 'strong',
                    errorClass: 'text-danger is-invalid',
                    errorPlacement: function(error, element) {
                        const id = error.attr('id').replace(/\[\]/g, "_");
                        if($(`#${id}`).length) $(`#${id}`).remove();

                        error.appendTo(element.closest('.form-group'));
                    },
                    submitHandler: function(form) {
                        submitForm(form);
                    }
                });
            });
        }
    })(jQuery);

    const createForm = (actionUrl, method, data) => {
        const form = document.createElement('form');
        form.method = method;
        form.action = actionUrl;

        for (const [name, values] of Object.entries(data)) {
            if (Array.isArray(values)) {
                values.forEach(value => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `${name}[]`;
                    input.value = value;
                    form.appendChild(input);
                });
            } else {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = values;
                form.appendChild(input);
            }
        }

        return form;
    }

    const validateForm = (selector) => {
        addValidationMethods();

        const isProfileOrUpdate = false;

        let rules = {
            password: {
                validate_password: true,
                maxlength: 50
            },
            confirm_password: {
                maxlength: 50,
                equalTo: "#password"
            },
            phone: {
                required: !isProfileOrUpdate,
                validate_mobile: true,
                digits: true
            },
            otp: {
                required: true,
                digits: true,
                maxlength: 6,
                minlength: 6
            },
            email: {
                required: !isProfileOrUpdate,
                maxlength: 100,
                email: true,
                email_id: true
            },
            alternative_email: {
                maxlength: 100,
                email: true,
                email_id: true
            },
            alternative_contact_number: {
                validate_mobile: true,
                digits: true
            },
            name: {
                required: true,
                maxlength: 100
            }
        };

        $(selector).each(function() {
            $(this).validate({
                rules: rules,
                errorElement: 'strong',
                errorClass: 'text-danger is-invalid',
                errorPlacement: function(error, element) {
                    const id = error.attr('id').replace(/\[\]/g, "_");
                    if($(`#${id}`).length) $(`#${id}`).remove();

                    error.appendTo(element.closest('.form-group'));
                },
                submitHandler: function(form) {
                    submitForm(form);
                }
            });
        });
    }

    const getData = (selector, actionUrl, method, data) => {
        const form = createForm(actionUrl, method, data);
        submitForm(form).done(function(response){
            $('.bs-tooltip').tooltip('dispose');

            if(response.html) {
                $(selector).html(response.html);
                if($('.bs-tooltip').length) {
                    $('.bs-tooltip').tooltip();
                }
            }
        });
    }

    $(document).on("click", ".submit-parent-form", function(){
        const form = this.closest('form');
        submitForm(form, true);
    });
</script>