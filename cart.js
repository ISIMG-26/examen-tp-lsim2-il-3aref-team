function getCart() {
  try { return JSON.parse(localStorage.getItem('cart') || '[]'); } catch { return []; }
}

function saveCart(cart) {
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartBadge();
}

function addToCart(product) {
  var cart = getCart();
  var existing = cart.find(function(i) { return i.id === product.id; });
  if (existing) {
    existing.qty += 1;
  } else {
    cart.push({ id: product.id, name: product.name, category: product.category,
                price: product.price, discount: product.discount || 0,
                image: product.image, qty: 1 });
  }
  saveCart(cart);
}

function updateCartBadge() {
  var total = getCart().reduce(function(sum, i) { return sum + i.qty; }, 0);
  document.querySelectorAll('.cart-count').forEach(function(el) {
    el.textContent = total;
    el.style.display = total > 0 ? 'flex' : 'none';
  });
}

updateCartBadge();


function renderCart() {
  var cart = getCart();
  var itemsBox   = document.getElementById('cart-items');
  var summaryBox = document.getElementById('cart-summary');
  if (!itemsBox) return;

  if (cart.length === 0) {
    itemsBox.innerHTML = '<div class="cart-empty"><h3>Your cart is empty</h3>' +
      '<p>Browse our products and add something you like.</p>' +
      '<a href="index.php" class="btn btn-dark">Shop Now</a></div>';
    summaryBox.style.display = 'none';
    return;
  }

  summaryBox.style.display = '';
  var html = '<div class="cart-items-box">';
  cart.forEach(function(item) {
    var final = item.discount > 0 ? item.price * (1 - item.discount / 100) : item.price;
    var total = '$' + (final * item.qty).toFixed(2);
    html += '<div class="cart-item" data-id="' + item.id + '">' +
      '<div class="cart-item-img"><img src="' + item.image + '" alt="' + item.name +
        '" onerror="this.src=\'\'"/></div>' +
      '<div>' +
        '<div class="cart-item-name">' + item.name + '</div>' +
        '<div class="cart-item-cat">' + item.category + '</div>' +
        '<div class="cart-item-bottom">' +
          '<div class="qty-ctrl">' +
            '<button onclick="changeQty(\'' + item.id + '\',-1)">−</button>' +
            '<input type="number" value="' + item.qty + '" min="1" max="20"' +
              ' onchange="setQty(\'' + item.id + '\',this.value)" />' +
            '<button onclick="changeQty(\'' + item.id + '\',1)">+</button>' +
          '</div>' +
          '<button class="remove-btn" onclick="removeItem(\'' + item.id + '\')">Remove</button>' +
        '</div>' +
      '</div>' +
      '<div class="cart-item-price">' + total + '</div>' +
    '</div>';
  });
  html += '</div>';
  itemsBox.innerHTML = html;

  var subtotal = cart.reduce(function(s, i) {
    return s + (i.discount > 0 ? i.price * (1 - i.discount / 100) : i.price) * i.qty;
  }, 0);
  var shipping = subtotal > 100 ? 0 : 9.99;

  document.getElementById('summary-subtotal').textContent = '$' + subtotal.toFixed(2);
  document.getElementById('summary-shipping').textContent = shipping === 0 ? 'Free' : '$' + shipping.toFixed(2);
  document.getElementById('summary-total').textContent    = '$' + (subtotal + shipping).toFixed(2);
  document.getElementById('item-count').textContent = cart.reduce(function(s, i) { return s + i.qty; }, 0);
}

function changeQty(id, delta) {
  var cart = getCart();
  var item = cart.find(function(i) { return i.id === id; });
  if (!item) return;
  item.qty = Math.max(1, item.qty + delta);
  saveCart(cart);
  renderCart();
}

function setQty(id, val) {
  var cart = getCart();
  var item = cart.find(function(i) { return i.id === id; });
  if (!item) return;
  item.qty = Math.max(1, Math.min(20, parseInt(val) || 1));
  saveCart(cart);
  renderCart();
}

function removeItem(id) {
  saveCart(getCart().filter(function(i) { return i.id !== id; }));
  renderCart();
}
