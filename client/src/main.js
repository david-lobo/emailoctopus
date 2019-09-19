import UserForm from './ContactForm';

$(document).ready(function() {
  let form = new UserForm("#contactForm");
  form.init();
});