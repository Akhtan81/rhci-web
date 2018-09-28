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

    it('`postalCodes` should return the initial state', () => {
        const result = reducer(undefined, {}).postalCodes

        expect(typeof result).toEqual("object")

        const keys = Object.keys(result)

        expect(keys.length).toEqual(1)

        keys.forEach(key => {
            expect(result[key].postalCode).toEqual(null)
            expect(result[key].type).toEqual(null)
            expect(result[key].cid).toEqual(key)
        })
    })

    it('`requests` should return the initial state', () => {
        expect(reducer(undefined, {}).requests).toEqual([])
    })
})