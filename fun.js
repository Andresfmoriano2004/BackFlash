function handleSubmit(event) {
    event.preventDefault(); // Previene el envío tradicional del formulario

    // Obtener los valores de los campos del formulario
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;

    // Mostrar los valores en la consola
    console.log('Nombre:', name);
    console.log('Correo Electrónico:', email);
    console.log('Asunto:', subject);
    console.log('Mensaje:', message);

    // También puedes mostrar un mensaje de éxito si lo deseas
    alert('Formulario enviado. Revisa la consola para ver los datos.');
    
    // Si lo deseas, puedes resetear el formulario después de enviarlo
    document.getElementById('contactForm').reset();
  }