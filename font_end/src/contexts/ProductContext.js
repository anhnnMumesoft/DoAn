
import React,{createContext,useState,useEffect} from 'react';


//create context
export const ProductContext = createContext();

const ProductProvider = ({children}) => {
  // product state
  const [products,setProducts] = useState([]);
  useState(()=>{
    const featchProducts = async ()=>{
      const response = await fetch('https://fakestoreapi.com/products');
      const data = await response.json();
      setProducts(data);
    }
    featchProducts();
  })
  return <ProductContext.Provider value={ {products} }>
    {children}
  </ProductContext.Provider>
};

export default ProductProvider;
