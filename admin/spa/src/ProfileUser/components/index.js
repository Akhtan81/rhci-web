import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import {MODEL_CHANGED} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";
import UploadMedia from "../actions/UploadMedia";
import PaymentInfo from "./PaymentInfo";

class ProfileUser extends React.Component {

    componentWillMount() {

        setTitle(translator('navigation_profile'))

        this.props.dispatch(FetchItem())
    }

    submit = () => {
        const {model} = this.props.ProfileUser

        this.props.dispatch(Save(model))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeString = name => e => this.change(name, e.target.value)

    getError = key => {
        const {errors} = this.props.ProfileUser.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    uploadAvatar = (e) => {
        const file = e.target.files[0]
        if (!file) return

        this.props.dispatch(UploadMedia(file))
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.ProfileUser
        const {isAdmin} = this.props

        const isDemo = model.id && model.isDemo

        return <div>
            <div className="card my-3">
                <div className="card-header">
                    <div className="row">
                        <div className="col-6">
                            <h4 className="m-0">{translator('navigation_profile')}</h4>
                        </div>
                        <div className="col-6 text-right">

                            <button className="btn btn-success btn-sm"
                                    disabled={!isValid || isLoading}
                                    onClick={this.submit}>
                                <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                                &nbsp;{translator('save')}
                            </button>

                            {isSaveSuccess && <div className="text-muted c-green-500">
                                <i className="fa fa-check"/>&nbsp;{translator('save_success_alert')}
                            </div>}
                        </div>
                    </div>
                </div>

                <div className="card-body">
                    <div className="row">
                        <div className="col">

                            {serverErrors.length > 0 && <div className="alert alert-danger">
                                <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                            </div>}


                            <div className="row">
                                <div className="col-12 col-md-6 col-lg-3">

                                    <div className="img-container text-center">
                                        {!isLoading && model.avatar
                                            ? <img src={model.avatar.url} className="img-fluid"/>
                                            : null}
                                    </div>

                                    <div className="form-group">
                                        <label>{translator('avatar')}</label>
                                        <input type="file"
                                               name="avatar"
                                               className="form-control"
                                               accept="image/png,image/jpg,image/jpeg,image/gif,image/bmp"
                                               onChange={this.uploadAvatar}/>
                                        {this.getError('avatar')}
                                    </div>

                                </div>

                                <div className="col-12 col-md-6 col-lg-5">

                                    <div className="row">
                                        <div className="col-12">
                                            <div className="form-group">
                                                <label className="required">{translator('name')}</label>
                                                <input type="text"
                                                       name="name"
                                                       className="form-control"
                                                       onChange={this.changeString('name')}
                                                       value={model.name || ''}/>
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
                                                       disabled={isDemo}
                                                       value={model.email || ''}/>
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
                                                       value={model.phone || ''}/>
                                                {this.getError('phone')}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="row">
                                        <div className="col-12 col-sm-6">

                                            <div className="form-group">
                                                <label
                                                    className={!model.id ? "required" : ""}>{translator('password')}</label>
                                                <input type="password"
                                                       name="password"
                                                       className="form-control"
                                                       onChange={this.changeString('password')}
                                                       disabled={isDemo}
                                                       value={model.password || ''}/>
                                                {this.getError('password')}
                                            </div>

                                        </div>
                                        <div className="col-12 col-sm-6">

                                            <div className="form-group">
                                                <label
                                                    className={!model.id ? "required" : ""}>{translator('password_repeat')}</label>
                                                <input type="password"
                                                       name="password2"
                                                       className="form-control"
                                                       onChange={this.changeString('password2')}
                                                       disabled={isDemo}
                                                       value={model.password2 || ''}/>
                                                {this.getError('password2')}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {AppParameters.payments.stripe.isEnabled && !isAdmin && <PaymentInfo/>}
        </div>
    }
}

export default withRouter(connect(selectors)(ProfileUser))
