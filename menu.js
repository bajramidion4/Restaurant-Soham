 let order = [];
let total = 0;

const orderList = document.getElementById("order-list");
const totalPriceEl = document.getElementById("total-price");

document.querySelectorAll(".menu-item").forEach(item => {
  item.addEventListener("click", () => {
    const name = item.dataset.name;
    const price = parseFloat(item.dataset.price);

    order.push({ name, price });
    total += price;
    updateOrder();
  });
});

function updateOrder() {
  orderList.innerHTML = "";
  total = 0;

  order.forEach((item, index) => {
    total += item.price;

    const li = document.createElement("li");
    li.textContent = `${item.name} - €${item.price.toFixed(2)}`;
    li.style.cursor = "pointer";

  // ✅ Remove item when clicked
    li.addEventListener("click", () => {
      removeItem(index);
    });

    orderList.appendChild(li);
  });
  