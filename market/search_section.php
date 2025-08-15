<div class="container pt-5">
    <div class="row">
        <div class="col-lg-6">
            <div class="section-heading">
                <div class="input-group mb-3">
                    <input type="text" id="productSearch" class="form-control border border-dark" placeholder="Tafuta bidhaa...">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <div id="searchSuggestions" style="position: relative; width: 100%; background-color: #fff; z-index: 1000;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('productSearch').addEventListener('keyup', function() {
        var searchQuery = this.value;

        if (searchQuery.length >= 2) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'search_products.php?q=' + encodeURIComponent(searchQuery), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var suggestions = JSON.parse(xhr.responseText);
                    var suggestionBox = document.getElementById('searchSuggestions');
                    suggestionBox.innerHTML = '';

                    suggestions.forEach(function(product) {
                        var suggestionItem = document.createElement('div');
                        suggestionItem.style.padding = '10px';
                        suggestionItem.style.cursor = 'pointer';
                        suggestionItem.style.borderBottom = '1px solid #ccc';
                        suggestionItem.style.display = 'flex';
                        suggestionItem.style.alignItems = 'center';

                        // Create image element
                        var productImage = document.createElement('img');
                        productImage.src = 'assets/images/' + product.image; // Assuming the image path is correctly stored in the database
                        productImage.style.width = '50px';
                        productImage.style.height = '50px';
                        productImage.style.objectFit = 'cover';
                        productImage.style.marginRight = '10px';

                        // Create text container
                        var textContainer = document.createElement('div');

                        textContainer.innerHTML = '<strong>' + product.name + '( Tsh.' + product.amount + ')' + '</strong><br><small>' + product.description + '</small>';

                        // Append image and text container to suggestion item
                        suggestionItem.appendChild(productImage);
                        suggestionItem.appendChild(textContainer);

                        suggestionItem.addEventListener('click', function() {
                            window.location.href = 'single-product.php?id=' + product.id;
                        });

                        suggestionBox.appendChild(suggestionItem);
                    });
                }
            };
            xhr.send();
        }
    });
</script>