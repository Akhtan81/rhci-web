import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';

import selectors from './selectors';
import AddSubscription from '../../actions/AddSubscription';
import CancelSubscription from '../../actions/CancelSubscription';
import FetchItems from '../../actions/FetchSubscriptions';
import translator from '../../../translations/translator';
import {renderSubscriptionStatus} from "../../../Order/utils";

class Subscriptions extends React.Component {

    componentWillMount() {
        this.props.dispatch(FetchItems())
    }

    addSubscription = () => {
        this.props.dispatch(AddSubscription())
    }

    cancelSubscription = () => {
        this.props.dispatch(CancelSubscription())
    }

    renderContent = () => {

        const {isLoading, items} = this.props.Subscriptions

        if (isLoading) return <div className="banner">
            <i className="fa fa-2x fa-spin fa-circle-o-notch"/>
        </div>

        if (items.length === 0) {
            return <div className="banner">
                <h4>{translator('no_subscriptions_title')}</h4>
            </div>
        }

        return <div className="table-responsive mb-3">
            <table className="table table-sm table-hover">
                <thead>
                <tr>
                    <th>{translator('plan')}</th>
                    <th>{translator('status')}</th>
                    <th>{translator('startedAt')}</th>
                    <th>{translator('finishedAt')}</th>
                </tr>
                </thead>
                <tbody>
                {items.map((item, i) => {
                    return <tr key={i}>
                        <td>{item.type}</td>
                        <td>{renderSubscriptionStatus(item.status)}</td>
                        <td>{item.startedAt}</td>
                        <td>{item.finishedAt}</td>
                    </tr>
                })}
                </tbody>
            </table>
        </div>
    }

    render() {

        const {isLoading, items} = this.props.Subscriptions

        const hasActive = items.find(item => item.status === 'active');

        return <div className="row">
            <div className="col-12 col-md-8 offset-md-2">

                <h4>{translator('my_subscriptions')}</h4>

                <div className="mb-2">
                    <button className="btn btn-sm btn-outline-primary mr-1"
                            disabled={isLoading || hasActive}
                            onClick={this.addSubscription}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-play"}/>
                        &nbsp;{translator('start_subscription')}
                    </button>
                    <button className="btn btn-sm btn-outline-danger mr-1"
                            disabled={isLoading || !hasActive}
                            onClick={this.cancelSubscription}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-times"}/>
                        &nbsp;{translator('cancel_subscription')}
                    </button>
                </div>

                {this.renderContent()}
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(Subscriptions))
