<script setup>

    // const items = [
    //     { name: 'item-1', quantity: 3, price: 12, total: 0 },
    //     { name: 'item-2', quantity: 4, price: 10, total: 0 },
    //     { name: 'item-3', quantity: 1, price: 5, total: 0 }
    // ];

    // const calcTotal = items.map(item => {
    //     return Number(item.quantity) * Number(item.price);
    // });

//const subtotal = 0;
//const subTotal = calcTotal.reduce((accumulator, currentValue) => accumulator + currentValue);
// const saleTax = 0.06 * Number(subTotal);

// console.log(JSON.stringify(calcTotal) + 'log');
// console.log('Sub Total: '+ subTotal);
// console.log('Sub Tax: '+ 0.06 * 100);

</script>

<template>
    <div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody v-scope="{
                items: [
                    { name: 'item-1', quantity: 3, price: 12, total: 0 },
                    { name: 'item-2', quantity: 4, price: 10, total: 0 },
                    { name: 'item-3', quantity: 1, price: 5, total: 0 }
                ],
                subtotal: 0
            }">
                <tr v-for="(item, index) in items" :key="index">
                    <td>{{item.name}}</td>
                    <td>{{item.quantity}}</td>
                    <td>{{item.price}}</td>
                    <td v-effect="item.total = Number(item.price) * Number(item.quantity)">
                        {{ item.total.toFixed(2)}}
                    </td>
                </tr>
                <tfoot>
                    <tr>
                        <td>Sub Total:</td>
                        <td v-effect="subtotal = items.length <= 1 ? items.map(i => Number(i.total)).reduce((acc, val) => acc +val) : 0">
                            {{ subtotal }}
                        </td>
                    </tr>
                    <tr>
                        <td>Tax Total:</td>
                        <!-- <td v-effect="taxtotal = Number(subtotal)">
                            {{ taxtotal.toFixed(2) }}
                        </td> -->
                    </tr>
                </tfoot>
            </tbody>
        </table>
    </div>
</template>
