import reducer from '../../../src/PartnerEdit/reducers/model'

describe('`PartnerEdit` model reducer', () => {

    it('`id` should return the initial state', () => {
        expect(reducer(undefined, {}).id).toEqual(null)
    })

    it('`createdAt` should return the initial state', () => {
        expect(reducer(undefined, {}).createdAt).toEqual(null)
    })

    it('`status` should return the initial state', () => {
        expect(reducer(undefined, {}).status).toEqual(null)
    })

    it('`country` should return the initial state', () => {
        expect(reducer(undefined, {}).country).toEqual(null)
    })

    it('`requests` should return the initial state', () => {
        expect(reducer(undefined, {}).requests).toEqual([])
    })

    it('`postalCodesRecycling` should return the initial state', () => {
        expect(reducer(undefined, {}).postalCodesRecycling).toEqual(null)
    })

    it('`postalCodesJunkRemoval` should return the initial state', () => {
        expect(reducer(undefined, {}).postalCodesJunkRemoval).toEqual(null)
    })

    it('`postalCodesShredding` should return the initial state', () => {
        expect(reducer(undefined, {}).postalCodesShredding).toEqual(null)
    })
})