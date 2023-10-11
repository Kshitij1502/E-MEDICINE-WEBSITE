// cart.js
document.addEventListener('DOMContentLoaded', function () {
    loadCartItems();

    function loadCartItems() {
        // Send an AJAX request to fetch cart items
        fetch('get_cart.php')
            .then(response => response.json())
            .then(data => {
                const cartItemsContainer = document.getElementById('cart-items');
                cartItemsContainer.innerHTML = ''; // Clear existing items

                if (data.length === 0) {
                    cartItemsContainer.innerHTML = '<p class="empty">Your cart is empty</p>';
                } else {
                    data.forEach(cartItem => {
                        const cartItemElement = createCartItemElement(cartItem);
                        cartItemsContainer.appendChild(cartItemElement);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function createCartItemElement(cartItem) {
        const cartItemDiv = document.createElement('div');
        cartItemDiv.className = 'box';

        // Create other elements for the cart item display
        // You can customize this part based on your item structure

        // Create an "Update" button
        const updateButton = document.createElement('button');
        updateButton.textContent = 'Update';
        updateButton.addEventListener('click', () => {
            const newQuantity = prompt('Enter new quantity:', cartItem.quantity);
            if (newQuantity !== null) {
                updateCartItem(cartItem._id, parseInt(newQuantity));
            }
        });

        // Create a "Delete" button
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.addEventListener('click', () => {
            deleteCartItem(cartItem._id);
        });

        // Append elements to the cart item div
        cartItemDiv.appendChild(updateButton);
        cartItemDiv.appendChild(deleteButton);

        return cartItemDiv;
    }

    function updateCartItem(cartItemId, newQuantity) {
        // Send an AJAX request to update the cart item
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cartItemId,
                newQuantity,
            }),
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                loadCartItems(); // Refresh the cart items after updating
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function deleteCartItem(cartItemId) {
        // Send an AJAX request to delete the cart item
        fetch('delete_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cartItemId,
            }),
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                loadCartItems(); // Refresh the cart items after deleting
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
});
