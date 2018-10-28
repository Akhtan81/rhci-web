import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';

import selectors from './selectors';
import CancelSubscription from '../../actions/CancelSubscription';
import FetchItems from '../../actions/FetchSubscriptions';
import translator from '../../../translations/translator';
import {renderSubscriptionStatus} from "../../../Order/utils";

class Subscriptions extends React.Component {

    componentWillMount() {
        this.props.dispatch(FetchItems())
    }

    cancelSubscription = () => {
        this.props.dispatch(CancelSubscription())
    }

    render() {

        const {isLoading, items} = this.props.Subscriptions

        const item = items.find(item => item.status === 'active');

        if (!item) return null;

        return <div className="row">
            <div className="col-12">
                <div className="table-responsive mb-3">
                    <table className="table table-sm table-hover">
                        <thead>
                        <tr>
                            <th>{translator('action')}</th>
                            <th>{translator('status')}</th>
                            <th>{translator('startedAt')}</th>
                            <th>{translator('finishedAt')}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <button className="btn btn-sm btn-outline-danger mr-1"
                                        disabled={isLoading}
                                        onClick={this.cancelSubscription}>
                                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-times"}/>
                                    &nbsp;{translator('cancel_subscription')}
                                </button>
                            </td>
                            <td>{renderSubscriptionStatus(item.status)}</td>
                            <td>{item.startedAt}</td>
                            <td>{item.finishedAt}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(Subscriptions))
