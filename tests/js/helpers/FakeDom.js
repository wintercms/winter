import { JSDOM } from 'jsdom'

class FakeDom
{
    constructor(content, options)
    {
        // Header settings
        this.url = options.url || 'https://winter.example.org'
        this.referer = options.referer
        this.contentType = options.contentType || 'text/html'

        // Content settings
        this.head = options.head || '<!DOCTYPE html><html><head><title>Fake document</title></head>'
        this.bodyStart = options.bodyStart || '<body>'
        this.content = content || ''
        this.bodyEnd = options.bodyEnd || '</body>'
        this.foot = options.foot || '</html>'

        // Callback settings
        this.beforeParse = (typeof options.beforeParse === 'function')
            ? options.beforeParse
            : undefined

        // Scripts
        this.scripts = []
        this.inline = []
    }

    setContent(content)
    {
        this.content = content
        return this
    }

    addScript(script, id)
    {
        if (Array.isArray(script)) {
            script.forEach((item) => {
                this.addScript(item)
            })

            return this
        }

        let url = new URL(script, this.url)
        let base = new URL(this.url)

        if (url.host === base.host) {
            this.scripts.push({
                url: `file://.${url.pathname}`,
                id: id || this.generateId(),
            })
        } else {
            this.scripts.push({
                url,
                id: id || this.generateId(),
            })
        }

        return this
    }

    addInlineScript(script, id)
    {
        this.inline.push({
            script,
            id: id || this.generateId(),
            element: null,
        })

        return this
    }

    generateId()
    {
        let id = 'script-'
        let chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-'
        let charLength = chars.length

        for (let i = 0; i < 10; i++) {
            let currentChar = Math.floor(Math.random() * (charLength + 1))
            id = `${id}${currentChar}`
        }

        return id
    }

    render()
    {
        return new Promise((resolve, reject) => {
            try {
                const dom = new JSDOM(
                    this.renderContent(),
                    {
                        url: this.url,
                        referrer: this.referer,
                        contentType: this.contentType,
                        includeNodeLocations: true,
                        runScripts: 'dangerously',
                        resources: 'usable',
                        pretendToBeVisual: true,
                        beforeParse: this.beforeParse,
                    }
                )

                dom.window.doResolve = () => {
                    resolve(dom)
                }
            } catch (e) {
                reject(e)
            }
        })
    }
}

export default FakeDom
