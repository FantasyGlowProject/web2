document.addEventListener("DOMContentLoaded", function() {
    // Función para cargar los productos desde el backend
    async function cargarProductos() {
      try {
        // Hacemos una solicitud al archivo PHP para obtener los productos
        const response = await fetch('productos.php');
        const productos = await response.json();
  
        // Si hay productos, los mostramos en el frontend
        if (productos.length > 0) {
          const productosLista = document.querySelector('.productos-lista');
          productosLista.innerHTML = ''; // Limpiamos la lista antes de agregar los productos
  
          productos.forEach(producto => {
            const card = document.createElement('div');
            card.classList.add('producto-card');
            card.innerHTML = `
              <img src="${producto.Imagen}" alt="${producto.Nombre_Producto}">
              <h3>${producto.Nombre_Producto}</h3>
              <p>$${producto.Precio_Venta.toFixed(2)}</p>
              <button class="agregar-carrito" data-id="${producto.ID_Producto}">Agregar al carrito</button>
            `;
            productosLista.appendChild(card);
          });
        }
      } catch (error) {
        console.error('Error al cargar productos:', error);
      }
    }
  
    // Cargar los productos cuando la página esté lista
    cargarProductos();
  
    // Lógica para agregar productos al carrito
    const carrito = [];
    const productosLista = document.querySelector('.productos-lista');
    
    productosLista.addEventListener('click', function(e) {
      if (e.target.classList.contains('agregar-carrito')) {
        const productoId = e.target.getAttribute('data-id');
        // Buscamos el producto correspondiente
        const producto = productos.find(p => p.ID_Producto === parseInt(productoId));
        carrito.push(producto);
        actualizarCarrito();
      }
    });
  
    // Función para actualizar el carrito de compras
    function actualizarCarrito() {
      const itemsCarrito = document.getElementById('items-carrito');
      itemsCarrito.innerHTML = '';
      let total = 0;
  
      carrito.forEach(producto => {
        const li = document.createElement('li');
        li.innerHTML = `${producto.Nombre_Producto} - $${producto.Precio_Venta.toFixed(2)}`;
        itemsCarrito.appendChild(li);
        total += producto.Precio_Venta;
      });
  
      document.getElementById('total-carrito').textContent = total.toFixed(2);
    }
  
    // Mostrar carrito
    document.getElementById('carrito').style.display = 'block';
  
    // Evento de pago
    document.getElementById('pagar-btn').addEventListener('click', function() {
      alert('Proceso de pago iniciado');
    });
  });
  
  