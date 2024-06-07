# Sales & Product structure

## Product/Categories

Editing categories must be from `/api/v1/categories` endpoint.

Category record structure:

~~~javascript
{
  id: 1,
  name: 'Whatever',
  type:  'good', // choices = {"good", "labor"}
  appliesTo:  'windows', // choices = {"windows", "roofing", "siding"}
  products: [
    {
      id: 20,
      categoryId: '', // Just the id, for consistency on saving
      name: 'Some SubItem size 10',
      fullName: 'Whatever Some SubItem size 10', // calculated = category.name + ' ' + product.name
      costAmount: 120.20,
      costMethod: 'fixed', // choices = {"fixed", "sqft"}
    },
    {
      id: 21,
      categoryId: '', // Just the id, for consistency on saving
      name: 'Some SubItem size 25',
      fullName: 'Whatever Some SubItem size 25', // calculated = category.name + ' ' + product.name
      costAmount: 300.50,
      costMethod: 'fixed', // choices = {"fixed", "sqft"}
    }
  ]
}
~~~

## Sales structure

`Sale` has `products (array of SaleProduct)` - it's a relationship
with attributes.

~~~javascript
{
  id: 5050,
  appointment: {},
  appointmentId: '', // Redundant, ignored on update
  soldPrice: '',
  soldPercentage: '',
  salesTax: '',
  amountDue: '',
  amountOwned: '',
  paymentType: '',
  discount: '',
  jobCeiling: '',
  notes: '',
  status: '', //choices = {"approved", "on-hold", "completed", "declined", "canceled"}
              // if NULL default to "on-hold"
  soldOnDate: '',
  netOnDate: '',
  paidDate: '',
  createdAt: '',
  createdBy: '',
  customer: '',
  office: '',
  histories: [],
  attachments: [],
  projectId: '', // Not updatable, only returned to create link to project
  products: [
    {
      id: 224,
      product: {
        id: 20,
        categoryId: 1,
        category: {
          name: 'Whatever',
          type:  'good',
          appliesTo:  'windows'
        },
        name: 'Some SubItem size 10',
        fullName: 'Whatever Some SubItem size 10',
        costAmount: 120.20,
        costMethod: 'fixed',  
      },
      productId: 21,
      saleId: 5050,
      quantity: 4,
      unitPrice: 120.20,
    },
    {
      id: 225,
      product: {
        id: 210,
        categoryId: 1,
        category: {
          name: 'Whatever',
          type:  'good',
          appliesTo:  'windows'
        },
        name: 'Some SubItem size 25',
        fullName: 'Whatever Some SubItem size 25',
        costAmount: 300.50,
        costMethod: 'fixed'
      },
      productId: 21,
      saleId: 5050,
      quantity: 4,
      unitPrice: 300.50,
    }
  ]
}
~~~

Relation:

~~~bash
Sale
- product: array of (SaleProduct)
  - Product
    - Category
~~~

When inserting a new `product` on `Sale` you must send:

~~~javascript
{
  /*...*/
  products: [
    {
      productId: 21,
      quantity: 4,
      unitPrice: 300.50
    }
  ]
}
~~~

When **updating** you must send the `id` of each item, or null for new ones.

~~~javascript
{
  /*...*/
  products: [
    {
      id: 225,
      productId: 21,
      quantity: 6,
      unitPrice: 300.50
    },
    {
      productId: 20,
      quantity: 3,
      unitPrice: 120.20
    }
  ]
}
~~~

If you want to remove an item, just remove from the array: backend will
check the missing and remove it.

**OBS**: `Sale.jobCeiling` is updated on PreUpdate/PrePersist hook, so
whatever value sent it will be calculated anyway before saving - loop over
`products` and make SUM(`quantity` times `unitPrice`).
