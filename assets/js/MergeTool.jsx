import React, { Component} from "react";
import "../styles/MergeTool.css";
import {useState} from "react";
import Form from "./Form";

const URL = 'http://localhost:81/api/variants.jsonapi?filter[sku][]=0S-LN-16465-149-EE&filter[sku][]=1B-CE-6550-EE';

const MergeTool = () => {
    const [form, setForm] = useState({
        "data" : []
    });
    const formRenderer = () => {

    }
    const load = async (e) => {
        e.preventDefault();
        await fetch(URL)
            .then((res) => res.json())
            .then((json) => {
                setForm(json);
            })
    };
    return <div>
        <h1>MergeTool init</h1>
        <form onSubmit={load}>
            <label>
                Good SKU:
                <input type="text" name={'good_sku'}  />
            </label>
            <label>
                Bad SKU:
                <input type="text" name={'good_sku'}   />
            </label>
            <input type="submit" value="Submit" />
        </form>
        {form && form.data && form.data[0] &&
            <Form goodSkuData={form.data[0]} badSkuData={form.data[1]} />
        }
    </div>;
}

export default MergeTool;