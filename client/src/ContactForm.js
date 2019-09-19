export default class ContactForm {
    constructor(form) {
        this.$form = $(form);
        this.$submit = null;
        this.$card = null;
    }
    init() {
        this.$submit = this.$form.find('button');
        this.$card = this.$form.closest('.card');
        this.$wrap = this.$form.closest('.form-wrapper');

        this.bindValidation();
        this.bindSuccess();
        this.bindSubmit();
    }

    bindSuccess() {
        let vm = this;
        this.$card.on('animationend', function() {
            vm.$card.remove();
            let $success = $("#successTemplate").html();
            vm.$wrap.append($success);
            $('.success-card').removeClass('hide').addClass('slide-in-top');
        });
    }
    bindSubmit() {
        let vm = this;
        this.$submit.on('click', function() {
            let validator = vm.$form.validate();
            validator.form();
            if (vm.$form.valid()) {
                $.post("http://localhost:8080/contacts",
                vm.$form.serialize(),
                function(data, status){
                    //alert("Data: " + data + "\nStatus: " + status);
                    if (data.hasOwnProperty('success') && data.success === true) {
                        console.log('Success')
                        $('body').addClass('overflow-hidden');
                        $('.form-card').addClass('slide-out-bottom');
                    } else {
                        if (data.hasOwnProperty('error')) {
                            let error = data.error;
                            let fields = data.error.fields;

                            console.log('Failed', fields);
                            var validator = vm.$form.validate();
                            validator.showErrors(fields);
                        }
                    }
                });
            }
        })
    }

    bindValidation() {
        this.$form.validate({
            rules: {
                FirstName: "required",
                LastName: "required",
                email_address: {
                    required: true,
                    email: true
                }
            },
            messages: {
                FirstName: "Please enter your first name",
                LastName: "Please enter your last name",
                email_address: "Please enter a valid email address",
            },
            errorElement: "em",
            errorPlacement: function(error, element) {
                // Add the `invalid-feedback` class to the error element
                error.addClass("invalid-feedback");
                if (element.prop("type") === "checkbox") {
                    error.insertAfter(element.next("label"));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).addClass("is-valid").removeClass("is-invalid");
            }
        });
    }
}