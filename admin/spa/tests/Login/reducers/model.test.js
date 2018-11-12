import reducer from '../../../src/OrderEdit/reducers/model'

describe('`OrderEdit` model reducer', () => {

    it('`id` should return the initial state', () => {
        expect(reducer(undefined, {}).id).toEqual(null)
    })

    it('`createdAt` should return the initial state', () => {
        expect(reducer(undefined, {}).createdAt).toEqual(null)
    })

    it('`type` should return the initial state', () => {
        expect(reducer(undefined, {}).type).toEqual(null)
    })

    it('`user` should return the initial state', () => {
        expect(reducer(undefined, {}).user).toEqual(null)
    })

    it('`partner` should return the initial state', () => {
        expect(reducer(undefined, {}).partner).toEqual(null)
    })

    it('`price` should return the initial state', () => {
        expect(reducer(undefined, {}).price).toEqual(null)
    })

    it('`location` should return the initial state', () => {
        expect(reducer(undefined, {}).location).toEqual(null)
    })

    it('`repeatable` should return the initial state', () => {
        expect(reducer(undefined, {}).repeatable).toEqual(null)
    })

    it('`status` should return the initial state', () => {
        expect(reducer(undefined, {}).status).toEqual(null)
    })

    it('`updatedAt` should return the initial state', () => {
        expect(reducer(undefined, {}).updatedAt).toEqual(null)
    })

    it('`updatedBy` should return the initial state', () => {
        expect(reducer(undefined, {}).updatedBy).toEqual(null)
    })

    it('`scheduledAt` should return the initial state', () => {
        expect(reducer(undefined, {}).scheduledAt).toEqual(null)
    })

    it('`isScheduleApproved` should return the initial state', () => {
        expect(reducer(undefined, {}).isScheduleApproved).toEqual(false)
    })

    it('`isPriceApproved` should return the initial state', () => {
        expect(reducer(undefined, {}).isPriceApproved).toEqual(false)
    })

    it('`items` should return the initial state', () => {
        expect(reducer(undefined, {}).items).toEqual([])
    })

    it('`payments` should return the initial state', () => {
        expect(reducer(undefined, {}).payments).toEqual([])
    })

    it('`messages` should return the initial state', () => {
        expect(reducer(undefined, {}).messages).toEqual([])
    })

    it('`statusReason` should return the initial state', () => {
        expect(reducer(undefined, {}).statusReason).toEqual(null)
    })
})