import React, {Component} from "react";
import "../styles/MergeTool.css";
import {useState} from "react";

const Form = ({goodSkuData, badSkuData}) => {
    console.log(goodSkuData);
    console.log(badSkuData);
    return (
        <div>
            <h3>Description</h3>
            <input value={goodSkuData.attributes.description} readOnly={true}/>
            <input value={badSkuData.attributes.description} readOnly={true}/>
            <h3></h3>
        </div>
    )
}

export default Form;