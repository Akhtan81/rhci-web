import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import {ADD_POSTAL_CODE, MODEL_CHANGED, REMOVE_POSTAL_CODE} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import translator from '../../translations/translator';
import {cid, objectValues, setTitle} from "../../Common/utils";

class PartnerRegister extends React.Component {

    componentWillMount() {
        setTitle(translator('navigation_partners_register'))
    }

    submit = () => {
        const {model} = this.props.PartnerRegister

        this.props.dispatch(Save(model))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeBool = name => e => this.change(name, e.target.checked)

    changeString = name => e => this.change(name, e.target.value)

    changeRequestType = (cid) => e => {

        let value = e.target.value
        if (value === 'none') value = null

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                request: {
                    cid,
                    type: value
                }
            }
        })
    }

    changeRequestPostalCode = cid => e => {

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                request: {
                    cid,
                    postalCode: e.target.value.replace(/[^0-9]/g, '')
                }
            }
        })
    }

    removePostalCode = cid => () => {

        this.props.dispatch({
            type: REMOVE_POSTAL_CODE,
            payload: {
                cid,
            }
        })
    }

    addPostalCode = () => {

        this.props.dispatch({
            type: ADD_POSTAL_CODE,
            payload: {
                cid: cid(),
                postalCode: null,
                type: null
            }
        })
    }

    getError = key => {
        const {errors} = this.props.PartnerRegister.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    renderPostalCodes() {

        const {model} = this.props.PartnerRegister

        const requests = objectValues(model.requestedPostalCodes)

        return <div className="row">

            {requests.map((request, i) => {

                return <div key={i}
                            className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">

                    <div className="form-group">
                        <div className="input-group">

                            {requests.length > 1 ?
                                <div className="input-group-prepend">
                                    <button className="btn btn-outline-secondary"
                                            onClick={this.removePostalCode(request.cid)}>
                                        <i className="fa fa-times"/>
                                    </button>
                                </div> : null}

                            <input type="text"
                                   name="postalCode"
                                   className="form-control"
                                   placeholder={translator('postal_code')}
                                   onChange={this.changeRequestPostalCode(request.cid)}
                                   value={request.postalCode || ""}/>

                            <div className="input-group-append">
                                <select name="type"
                                        value={request.type || 'none'}
                                        onChange={this.changeRequestType(request.cid)}
                                        className="form-control">
                                    <option value="none">{translator("select_type")}</option>
                                    <option value="junk_removal">{translator("order_types_junk_removal")}</option>
                                    <option value="recycling">{translator("order_types_recycling")}</option>
                                    <option disabled={true}
                                            value="shredding">{translator("order_types_shredding")}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            })}
        </div>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.PartnerRegister

        return <div className="container">
            <div className="row">
                <div className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">
                    <div className="card shadow-sm my-4">
                        <div className="card-body">

                            <h2 className="text-center">{translator('navigation_partners_register')}</h2>
                            <p className="text-center">{translator('navigation_partners_register_description')}</p>

                            <p>{translator('register_already_user')}
                                &nbsp;<Link to="/login">{translator('signin')}</Link></p>

                            {serverErrors.length > 0 && <div className="alert alert-danger">
                                <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                            </div>}

                            {isSaveSuccess && <div className="alert alert-success">
                                <div>{translator('partner_signup_success_notice')}</div>
                            </div>}

                            <div className="row">
                                <div className="col-12">

                                    <div className="row">
                                        <div className="col-12">

                                            <h4>{translator('personal_information')}</h4>

                                            <div className="row">
                                                <div className="col">
                                                    <div className="form-group">
                                                        <label className="required">{translator('name')}</label>
                                                        <input type="text"
                                                               name="name"
                                                               className="form-control"
                                                               onChange={this.changeString('name')}
                                                               value={model.user.name || ''}/>
                                                        {this.getError('name')}
                                                    </div>
                                                </div>
                                                <div className="col">
                                                    <div className="form-group">
                                                        <label className="required">{translator('email')}</label>
                                                        <input type="email"
                                                               name="email"
                                                               className="form-control"
                                                               onChange={this.changeString('email')}
                                                               value={model.user.email || ''}/>
                                                        {this.getError('email')}
                                                    </div>
                                                </div>
                                                <div className="col">

                                                    <div className="form-group">
                                                        <label>{translator('phone')}</label>
                                                        <input type="text"
                                                               name="phone"
                                                               className="form-control"
                                                               onChange={this.changeString('phone')}
                                                               value={model.user.phone || ''}/>
                                                        {this.getError('phone')}
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="row">
                                                <div className="col-12 col-md-6">
                                                    <div className="form-group">
                                                        <label
                                                            className={!model.id ? "required" : ""}>{translator('password')}</label>
                                                        <input type="password"
                                                               name="password"
                                                               className="form-control"
                                                               onChange={this.changeString('password')}
                                                               value={model.user.password || ''}/>
                                                        {this.getError('password')}
                                                    </div>
                                                </div>

                                                <div className="col-12 col-md-6">
                                                    <div className="form-group">
                                                        <label
                                                            className={!model.id ? "required" : ""}>{translator('password_repeat')}</label>
                                                        <input type="password"
                                                               name="password2"
                                                               className="form-control"
                                                               onChange={this.changeString('password2')}
                                                               value={model.user.password2 || ''}/>
                                                        {this.getError('password2')}
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="row">
                                        <div className="col-12">

                                            <h4>{translator('partner_location')}</h4>

                                            <div className="row">
                                                <div className="col-12 col-lg-8">
                                                    <div className="form-group">
                                                        <label className="required">{translator('address')}</label>
                                                        <textarea
                                                            name="address"
                                                            className="form-control"
                                                            onChange={this.changeString('address')}
                                                            value={model.location.address || ''}/>
                                                        {this.getError('address')}
                                                    </div>
                                                </div>

                                                <div className="col-12 col-lg-4">
                                                    <div className="form-group">
                                                        <label>{translator('postal_code')}</label>
                                                        <input type="text"
                                                               name="postalCode"
                                                               className="form-control"
                                                               onChange={this.changeString('postalCode')}
                                                               value={model.location.postalCode || ''}/>
                                                        {this.getError('postalCode')}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-12">

                                            <h4 className="text-center">{translator('partner_postal_codes')}</h4>

                                            {this.renderPostalCodes()}

                                            <div className="row">
                                                <div className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">
                                                    <div className="form-group text-right">
                                                        <button className="btn btn-sm btn-outline-success"
                                                                onClick={this.addPostalCode}>
                                                            <i className="fa fa-plus"/>&nbsp;{translator('add')}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="col-12">
                                    <div className="form-group text-center">
                                        <label className="required">
                                            <input type="checkbox"
                                                   name="isAccepted"
                                                   onChange={this.changeBool('isAccepted')}
                                                   checked={model.isAccepted}/>
                                            &nbsp;{translator('partner_register_terms')}</label>
                                        {this.getError('isAccepted')}
                                    </div>
                                    <div className="form-group text-center">
                                        <button className="btn btn-lg btn-success"
                                                onClick={this.submit}
                                                disabled={isLoading || !isValid}>
                                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                                            &nbsp;{translator('signup')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PartnerRegister))
