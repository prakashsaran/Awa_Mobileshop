# Awa_Mobileshop
magento 2 rest api for wishlist and product review

# get product review by product id 
http://domainname.com/rest/V1/awa-mobileshop/product/reviews/{productId} => Method GET



# get review by review id
http://domainname.com/rest/V1/awa-mobileshop/reviews/{reviewID}  => Method GET


# get review by customer id
http://domainname.com/rest/V1/awa-mobileshop/customer/reviews/{customerId} }  => Method GET


# create product review

http://domainname.com/rest/V1/awa-mobileshop/product/reviews }  => Method POST


data format 

{"review":{
	"title":"hello this test",
	"detail":"hello this test",
	"nickname":"hello",
	"ratings":[{"rating_name":"Quality","value":3},{"rating_name":"Price","value":2}],
	"review_entity": "product",
    "review_status": 2,
    "entity_pk_value": 2,
	"customer_id":3
}
}
